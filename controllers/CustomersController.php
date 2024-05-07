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
        $this->cookie_manager = new CookieManager();

        HeaderHelper::setResponseHeaders();
    }

    public function getAllCustomers()
    {

        $lists_of_customers = $this->customer_model->getAllCustomers();

        if (empty($lists_of_customers)) {
            ResponseHelper::sendSuccessResponse([], 'No customers found', 200);
            return;
        }

        ResponseHelper::sendSuccessResponse($lists_of_customers, 'Customers fetched successfully', 200);
    }

    public function getCustomerById()
    {

        $token = $this->cookie_manager->extractAccessTokenFromCookieHeader();

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

        $token = $this->cookie_manager->extractAccessTokenFromCookieHeader();
        $customer_id = $this->jwt->decodeJWTData($token)->id;

        $response = $this->customer_model->addNewUserAddress($customer_id, $data);

        if (!$response) {
            ResponseHelper::sendErrorResponse($response, 500);
            return;
        }

        ResponseHelper::sendSuccessResponse(null, 'Address added successfully', 201);
    }
}
