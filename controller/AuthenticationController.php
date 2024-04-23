<?php

use Helpers\JWTHelper;
use Helpers\ResponseHelper;
use Helpers\HeaderHelper;
use Helpers\CookieManager;

require_once dirname(__DIR__) . '/config/load_env.php';
require_once dirname(__DIR__) . '/model/CustomersModel.php';

class AuthenticationController
{
    private $jwt;
    private $customer_model;
    private $cookie_manager;

    public function __construct($pdo)
    {
        $this->jwt = new JWTHelper();
        $this->customer_model = new CustomersModel($pdo);
        $this->cookie_manager = new CookieManager($this->jwt);
    }

    public function register($payload)
    {

        if (!is_array($payload) || empty($payload)) {
            ResponseHelper::sendErrorResponse("Invalid payload or payload is empty");
            return;
        }

        HeaderHelper::setResponseHeaders();

        $password = $payload['password'];
        $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 15]);
        $payload['password'] = $hashed_password;

        $response = $this->customer_model->addNewCustomer($payload);

        if (is_string($response)) {
            ResponseHelper::sendErrorResponse($response, 500);
            return;
        }

        if (!$response) {
            ResponseHelper::sendErrorResponse('Failed to register new customer', 500);
            return;
        }

        ResponseHelper::sendSuccessResponse([], 'User registered successfully', 201);
    }

    public function login($payload)
    {
        if (!is_array($payload) || empty($payload)) {
            ResponseHelper::sendErrorResponse("Invalid payload or payload is empty", 400);
            return;
        }

        HeaderHelper::setResponseHeaders();

        $email = $payload['email'];
        $password = $payload['password'];

        $response = $this->customer_model->getAccountByEmail($email);

        if (!is_array($response) && !$response) {
            ResponseHelper::sendErrorResponse('Missing email payload');
            return;
        }

        if (empty($response)) {
            ResponseHelper::sendUnauthorizedResponse('Incorrect Email');
            return;
        }

        $stored_password = $response['data'][0]['password'];

        if (!password_verify($password, $stored_password)) {
            ResponseHelper::sendUnauthorizedResponse('Incorrect Password');
            return;
        }

        // 5hrs expiry time for token
        $expiry_date = time() + (5 * 3600);

        $to_be_tokenized = [
            "id" => $response['data'][0]['id'],
            'email' => $response['data'][0]['email'],
            'password' => $response['data'][0]['password'],
            'role' => $response['role'],
            'expiry_date' => $expiry_date
        ];

        $token = $this->jwt->encodeDataToJWT($to_be_tokenized);

        $this->cookie_manager->setCookiHeader($token, $expiry_date);

        ResponseHelper::sendSuccessResponse(['role' => $response['role']], 'Logged In success', 201);
    }

    public function logout()
    {
        $this->cookie_manager->resetCookieHeader();
        ResponseHelper::sendSuccessResponse([], 'Logout Succesfully', 200);
        return;
    }

    public function checkToken()
    {

        $this->cookie_manager->validateCookiePressence();

        $token = $this->cookie_manager->extractAccessTokenFromCookieHeader();

        if (!$this->jwt->validateToken($token)) {
            $this->cookie_manager->resetCookieHeader();
            ResponseHelper::sendUnauthorizedResponse('Invalid token');
            return;
        }

        ResponseHelper::sendSuccessResponse(null, 'Token is valid', 200);
    }
}
