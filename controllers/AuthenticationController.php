<?php

use Helpers\JWTHelper;
use Helpers\ResponseHelper;
use Helpers\HeaderHelper;
use Helpers\CookieManager;

use Validators\Authentication\LoginValidator;

use Models\CustomersModel;
use Models\EmployeesModel;

require_once dirname(__DIR__) . '/config/LoadEnvVariables.php';

class AuthenticationController
{
    private $jwt;
    private $customer_model;
    private $cookie_manager;
    private $employee_model;

    public function __construct($pdo)
    {
        $this->jwt = new JWTHelper();
        $this->customer_model = new CustomersModel($pdo);
        $this->cookie_manager = new CookieManager($this->jwt);
        $this->employee_model = new EmployeesModel($pdo);

        HeaderHelper::setResponseHeaders();
    }

    public function register($payload)
    {

        if (!is_array($payload) || empty($payload)) {
            ResponseHelper::sendErrorResponse("Invalid payload data type or payload is empty");
            exit;
        }

        if (!isset($payload['email']) || !isset($payload['password'])) {
            ResponseHelper::sendErrorResponse("Email or password is missing in the payload");
            exit;
        }

        if (!filter_var($payload['email'], FILTER_VALIDATE_EMAIL)) {
            ResponseHelper::sendErrorResponse("Invalid email format");
            exit;
        }


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
        $validatorMessage = LoginValidator::validatePayload($payload);

        if ($validatorMessage !== 'valid') {
            ResponseHelper::sendErrorResponse($validatorMessage, 400);
            return;
        }

        $email = filter_var($payload['email'], FILTER_SANITIZE_EMAIL);
        $password = filter_var($payload['password'], FILTER_SANITIZE_SPECIAL_CHARS);

        $customerAccounts = $this->customer_model->getAccountByEmail($email);
        $employeeAccounts = $this->employee_model->getEmployeeByEmail($email);

        if (!$customerAccounts && !$employeeAccounts) {
            ResponseHelper::sendUnauthorizedResponse('Account not found');
            return;
        }

        $userAccount = $customerAccounts ? $customerAccounts : $employeeAccounts;

        $stored_password = $userAccount['password'];

        if (!password_verify($password, $stored_password)) {
            ResponseHelper::sendUnauthorizedResponse('Incorrect Password');
            return;
        }

        // 5hrs expiry time for token
        $expiry_date = time() + (5 * 3600);

        $to_be_tokenized = [
            "id" => $userAccount['id'],
            'email' => $userAccount['email'],
            'password' => $userAccount['password'],
            'role' => $userAccount['role'],
            'expiry_date' => $expiry_date
        ];

        $token = $this->jwt->encodeDataToJWT($to_be_tokenized);

        $this->cookie_manager->setCookiHeader($token, $expiry_date);

        ResponseHelper::sendSuccessResponse(['role' => $userAccount['role']], 'Logged In success', 201);
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
