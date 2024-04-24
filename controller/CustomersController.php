<?php

use Helpers\JWTHelper;
use Helpers\ResponseHelper;
use Helpers\HeaderHelper;
use Helpers\CookieManager;

use Models\CustomersModel;

class CustomersController
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

    public function getAllCustomers()
    {
        $this->cookie_manager->validateCookiePressence();

        $token = $this->cookie_manager->extractAccessTokenFromCookieHeader();
        $is_token_valid = $this->jwt->validateToken($token);

        if (!$is_token_valid) {
            ResponseHelper::sendUnauthorizedResponse('Unauthorized');
            return;
        }

        $decoded_token = $this->jwt->decodeJWTData($token);

        if ($decoded_token->role !== 'admin') {
            ResponseHelper::sendUnauthorizedResponse('Unauthorized');
            return;
        }

        $lists_of_customers = $this->customer_model->getAllCustomers();

        if (empty($lists_of_customers)) {
            ResponseHelper::sendSuccessResponse([], 'No customers found', 200);
            return;
        }

        ResponseHelper::sendSuccessResponse($lists_of_customers, 'Customers fetched successfully', 200);
    }

    public function getCustomerById()
    {

        HeaderHelper::setResponseHeaders();

        $this->cookie_manager->validateCookiePressence();

        $token = $this->cookie_manager->extractAccessTokenFromCookieHeader();

        $is_token_valid = $this->jwt->validateToken($token);

        if (!$is_token_valid) {
            ResponseHelper::sendUnauthorizedResponse('Unauthorized');
            return;
        }

        $decoded_token = $this->jwt->decodeJWTData($token);
        $customer_id = $decoded_token->id;

        $response = $this->customer_model->getCustomerById($customer_id);

        if (empty($response)) {
            ResponseHelper::sendDatabaseErrorResponse('Failed to fetch account');
            return;
        }

        ResponseHelper::sendSuccessResponse($response, 'Successfully fetched account');
    }

    public function addNewUserAddress($data)
    {
        if (!is_array($data) || empty($data)) {
            ResponseHelper::sendErrorResponse("Invalid data or data is empty", 400);
            return;
        }

        HeaderHelper::setResponseHeaders();

        $headers = getallheaders();

        if (!isset($headers['Cookie'])) {
            $this->cookie_manager->resetCookieHeader();
            ResponseHelper::sendUnauthorizedResponse('Invalid Token');
            return;
        }

        $token = $headers['Cookie'];
        $token = str_replace("tcg_access_token=", "", $token);

        $customer_id = $this->jwt->decodeJWTData($token)->id;

        $response = $this->customer_model->addNewUserAddress($customer_id, $data);

        if (!$response) {
            ResponseHelper::sendErrorResponse($response, 500);
            return;
        }

        ResponseHelper::sendSuccessResponse(null, 'Address added successfully', 201);
    }
}
