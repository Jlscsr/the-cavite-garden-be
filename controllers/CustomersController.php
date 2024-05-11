<?php

use Helpers\JWTHelper;
use Helpers\ResponseHelper;
use Helpers\CookieManager;

use Validators\CustomersValidator;

use Models\CustomersModel;

class CustomersController
{
    private $jwt;
    private $customerModel;
    private $cookieManager;

    public function __construct($pdo)
    {
        $this->jwt = new JWTHelper();
        $this->customerModel = new CustomersModel($pdo);
        $this->cookieManager = new CookieManager();
    }

    /**
     * Retrieves all customers from the database.
     *
     * @throws RuntimeException If an error occurs while fetching customers.
     * @return void
     */
    public function getAllCustomers()
    {
        try {
            $customers = $this->customerModel->getAllCustomers();

            if (!$customers) {
                ResponseHelper::sendErrorResponse('No customers found', 404);
                exit;
            }

            ResponseHelper::sendSuccessResponse($customers, 'Customers fetched successfully', 200);
        } catch (RuntimeException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Retrieves the customer data by the customer ID obtained from the token.
     *
     * @throws RuntimeException if there is an error retrieving the customer data
     * @return void
     */
    public function getCustomerById(): void
    {
        try {
            $customerID = $this->getCostumerIDFromToken();

            $response = $this->customerModel->getCustomerById((int) $customerID);

            if (!$response) {
                ResponseHelper::sendErrorResponse('Failed to fetch account', 404);
                exit;
            }

            ResponseHelper::sendSuccessResponse($response, 'Successfully fetched account');
        } catch (RuntimeException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Adds a new user address.
     *
     * @param array $payload The payload containing the address information.
     * @throws RuntimeException If there is an error adding the address.
     * @throws InvalidArgumentException If the payload is invalid.
     * @return void
     */
    public function addNewUserAddress(array $payload): void
    {
        try {
            CustomersValidator::validateAddCustomerAddress($payload);

            $customerID = $this->getCostumerIDFromToken();

            $response = $this->customerModel->addNewUserAddress($customerID, $payload);

            if (!$response) {
                ResponseHelper::sendErrorResponse('Failed to add address', 400);
                exit;
            }

            ResponseHelper::sendSuccessResponse([], 'Address added successfully', 201);
        } catch (RuntimeException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        } catch (InvalidArgumentException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Retrieves the customer ID from the token.
     *
     * This function retrieves the customer ID from the token by validating the cookie presence,
     * extracting the access token from the cookie header, and decoding the JWT data.
     *
     * @return int The customer ID extracted from the token.
     */
    private function getCostumerIDFromToken(): int
    {
        $cookieHeader = $this->cookieManager->validateCookiePressence();
        $response = $this->cookieManager->extractAccessTokenFromCookieHeader($cookieHeader);
        $decodedToken = $this->jwt->decodeJWTData($response['token']);

        return $decodedToken->id;
    }
}
