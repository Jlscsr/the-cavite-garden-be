<?php

use Config\EnvironmentLoader;

use App\Models\CustomersModel;
use App\Models\EmployeesModel;

use App\Validators\AuthenticationValidator;

use App\Helpers\JWTHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\CookieManager;

class AuthenticationController
{
    private $jwt;
    private $customerModel;
    private $cookieManager;
    private $employeeModel;

    public function __construct($pdo)
    {
        $this->jwt = new JWTHelper();
        $this->customerModel = new CustomersModel($pdo);
        $this->cookieManager = new CookieManager();
        $this->employeeModel = new EmployeesModel($pdo);

        EnvironmentLoader::load();
    }

    /**
     * Registers a new user.
     *
     * @param array $payload The payload containing user registration data.
     *                      It should have the following keys:
     *                      - firstName: string
     *                      - lastName: string
     *                      - birthdate: string
     *                      - phoneNumber: string
     *                      - password: string
     *                      - email: string
     * @throws RuntimeException If an error occurs during registration.
     * @throws InvalidArgumentException If the payload is missing required fields.
     * @return void
     */
    public function register($payload)
    {
        try {
            AuthenticationValidator::validateRegisterPayload($payload);

            $payload['firstName'] = filter_var($payload['firstName'], FILTER_SANITIZE_SPECIAL_CHARS);
            $payload['lastName'] = filter_var($payload['lastName'], FILTER_SANITIZE_SPECIAL_CHARS);
            $payload['birthdate'] = filter_var($payload['birthdate'], FILTER_SANITIZE_SPECIAL_CHARS);
            $payload['phoneNumber'] = filter_var($payload['phoneNumber'], FILTER_SANITIZE_NUMBER_INT);
            $payload['customerEmail'] = filter_var($payload['customerEmail'], FILTER_SANITIZE_EMAIL);
            $payload['password'] = filter_var($payload['password'], FILTER_SANITIZE_SPECIAL_CHARS);

            $response = $this->customerModel->addNewCustomer($payload);

            if (!$response) {
                return ResponseHelper::sendErrorResponse('Failed to register new account', 400);
            }

            return ResponseHelper::sendSuccessResponse([], 'User registered successfully', 201);
        } catch (RuntimeException $e) {
            return ResponseHelper::sendErrorResponse($e->getMessage());
        } catch (InvalidArgumentException $e) {
            return ResponseHelper::sendErrorResponse($e->getMessage());
        }
    }

    /**
     * Performs user login authentication.
     *
     * @param array $payload The payload containing user login data.
     * @throws RuntimeException If an error occurs during login.
     * @return void
     */
    public function login(array $payload)
    {
        try {
            AuthenticationValidator::validateLoginPayload($payload);

            $email = filter_var($payload['email'], FILTER_SANITIZE_EMAIL);
            $password = filter_var($payload['password'], FILTER_SANITIZE_SPECIAL_CHARS);

            $userAccount = $this->customerModel->getCustomerByEmail($email) ?: $this->employeeModel->getEmployeeByEmail($email);

            if (!$userAccount || !password_verify($password, $userAccount['password'])) {
                return ResponseHelper::sendUnauthorizedResponse($userAccount ? 'Incorrect password' : 'Account not found');
            }

            $expiryDate = time() + (5 * 3600);
            $tokenData = [
                "id" => $userAccount['id'],
                'email' => $email,
                'role' => $userAccount['role'],
                'expiry_date' => $expiryDate
            ];

            unset($userAccount['password']);

            $token = $this->jwt->encodeDataToJWT($tokenData);

            $this->cookieManager->setCookiHeader($token, $expiryDate);

            return ResponseHelper::sendSuccessResponse($userAccount, 'Logged In success', 201);
        } catch (RuntimeException $e) {
            return ResponseHelper::sendErrorResponse($e->getMessage());
        } catch (InvalidArgumentException $e) {
            return ResponseHelper::sendErrorResponse($e->getMessage());
        }
    }

    public function getUserInfo()
    {
        try {
            $userID = $this->getCostumerIDFromToken();
            $userAccount = $this->customerModel->getCustomerById($userID) ?: $this->employeeModel->getEmployeeById($userID);

            if (!$userAccount) {
                return ResponseHelper::sendUnauthorizedResponse('Account not found');
            }

            return ResponseHelper::sendSuccessResponse($userAccount, 'User account fetched successfully', 200);
        } catch (RuntimeException $e) {
            return ResponseHelper::sendErrorResponse($e->getMessage());
        }
    }

    /**
     * Logs out the user by resetting the cookie header and sending a success response.
     *
     * @return void
     */
    public function logout()
    {
        $this->cookieManager->resetCookieHeader();
        ResponseHelper::sendSuccessResponse([], 'Logout Succesfully', 200);
        exit;
    }

    /**
     * Checks if the token in the cookie header is valid.
     *
     * @throws RuntimeException if an error occurs while validating the token
     * @return void
     */
    public function checkToken()
    {
        try {
            $cookieHeader = $this->cookieManager->validateCookiePressence();

            if (is_array($cookieHeader) && isset($cookieHeader['status']) && ($cookieHeader['status'] === 'failed')) {
                ResponseHelper::sendUnauthorizedResponse($cookieHeader['message']);
                exit;
            }

            $response = $this->cookieManager->extractAccessTokenFromCookieHeader(trim($cookieHeader));

            if (!$this->jwt->validateToken($response['token'])) {
                $this->cookieManager->resetCookieHeader();
                ResponseHelper::sendUnauthorizedResponse('Invalid token');
                exit;
            }

            ResponseHelper::sendSuccessResponse([], 'Token is valid', 200);
        } catch (RuntimeException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage());
        }
    }

    private function getCostumerIDFromToken(): string
    {
        $cookieHeader = $this->cookieManager->validateCookiePressence();
        $response = $this->cookieManager->extractAccessTokenFromCookieHeader($cookieHeader);
        $decodedToken = (object) $this->jwt->decodeJWTData($response['token']);

        return $decodedToken->id;
    }
}
