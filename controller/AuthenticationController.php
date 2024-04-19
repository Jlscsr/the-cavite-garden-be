<?php
require_once dirname(__DIR__) . '/config/load_env.php';

require_once dirname(__DIR__) . '/helpers/JWTHelper.php';
require_once dirname(__DIR__) . '/helpers/ResponseHelper.php';
require_once dirname(__DIR__) . '/helpers/HeaderHelper.php';
require_once dirname(__DIR__) . '/helpers/CookieManager.php';

require_once dirname(__DIR__) . '/model/AccountsModel.php';

class AuthenticationController
{
    private $jwt;
    private $accounts_model;
    private $cookie_manager;

    public function __construct($pdo)
    {
        $this->jwt = new JWTHelper();
        $this->accounts_model = new AccountsModel($pdo);
        $this->cookie_manager = new CookieManager($this->jwt);
    }

    public function register($credentials)
    {

        if (!is_array($credentials) || empty($credentials)) {
            ResponseHelper::sendErrorResponse("Invalid data or data is empty", 400);
            return;
        }

        HeaderHelper::setHeaders();

        $password = $credentials['password'];
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $credentials['password'] = $hashed_password;

        $response = $this->accounts_model->addNewAccount($credentials);

        if (!$response) {
            ResponseHelper::sendErrorResponse('Failed to register new customer', 500);
            return;
        }

        ResponseHelper::sendSuccessResponse(null, 'User registered successfully', 201);
    }

    public function login($credentials)
    {
        if (!is_array($credentials) || empty($credentials)) {
            ResponseHelper::sendErrorResponse("Invalid data or data is empty", 400);
            return;
        }

        HeaderHelper::setHeaders();

        $email = $credentials['email'];
        $password = $credentials['password'];

        $response = $this->accounts_model->getAccountByEmail($email);
        $stored_password = $response['data'][0]['password'];

        if (!password_verify($password, $stored_password)) {
            ResponseHelper::sendErrorResponse('Invalid email or password', 401);
            return;
        }

        $expiry_date = time() + 3600;

        $toBeTokenized = [
            "id" => $response['data'][0]['id'],
            'email' => $response['data'][0]['email'],
            'password' => $response['data'][0]['password'],
            'role' => $response['role'],
            'expiry_date' => $expiry_date
        ];

        $token = $this->jwt->encodeData($toBeTokenized);

        $this->cookie_manager->setCookiHeader($token, $expiry_date);

        ResponseHelper::sendSuccessResponse(['role' => $response['role']], 'Logged In success', 201);
    }

    public function logout()
    {
        $this->cookie_manager->resetCookieHeader();
        ResponseHelper::sendSuccessResponse(null, 'Logout Succesfully', 200);
        return;
    }

    public function checkToken()
    {
        $headers = getallheaders();

        if (!isset($headers['Cookie'])) {
            $this->cookie_manager->resetCookieHeader();
            ResponseHelper::sendUnauthrizedResponse('Invalid Token');
            return;
        }

        $token = $headers['Cookie'];
        $token = str_replace("tcg_access_token=", "", $token);

        $is_token_valid = $this->cookie_manager->validateToken($token);

        if (!$is_token_valid) {
            $this->cookie_manager->resetCookieHeader();
            ResponseHelper::sendUnauthrizedResponse('Invalid token');
            return;
        }

        ResponseHelper::sendSuccessResponse(null, 'Token is valid', 200);
    }
}
