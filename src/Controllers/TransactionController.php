<?php

namespace App\Controllers;

use InvalidArgumentException;
use RuntimeException;

use App\Models\TransactionModel;

use App\Validators\TransactionsValidator;

use App\Helpers\JWTHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\CookieManager;
use App\Models\HelperModel;

class TransactionController
{
    private $jwt;
    private $transactionModel;
    private $cookieManager;
    private $helperModel;

    public function __construct($pdo)
    {
        $this->jwt = new JWTHelper();
        $this->transactionModel = new TransactionModel($pdo);
        $this->cookieManager = new CookieManager();
        $this->helperModel = new HelperModel($pdo);
    }

    /**
     * Retrieves all transactions based on the provided parameter.
     *
     * @param array $parameter An array containing the parameters for filtering transactions.
     * @throws RuntimeException If an error occurs during the transaction retrieval process.
     * @return void
     */
    public function getAllTransactions(array $params): void
    {
        try {
            TransactionsValidator::validateGetTransactionsByParameter($params);

            $status = $params['status'];
            $orderPurpose = $params['orderPurpose'];

            $response = $this->transactionModel->getAllTransactions($status, $orderPurpose);

            if (!$response) {
                ResponseHelper::sendSuccessResponse([], 'No Transactions found');
                exit;
            }

            ResponseHelper::sendSuccessResponse($response, 'Successfully fetched transactions', 200);
        } catch (RuntimeException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        } catch (InvalidArgumentException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 400);
        }
    }

    public function getTransactionByCustomerID($params): void
    {
        try {
            TransactionsValidator::validateGetTransactionsByParameter($params);

            $customerID  = $params['customerID'];
            $response = $this->transactionModel->getTransactionByCustomerID($customerID);

            if (!$response) {
                ResponseHelper::sendSuccessResponse([], 'No Transactions found');
                exit;
            }

            ResponseHelper::sendSuccessResponse($response, 'Successfully fetched transactions', 200);
        } catch (RuntimeException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        } catch (InvalidArgumentException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Adds a new transaction to the database.
     *
     * @param array $payload An array containing the necessary data for the transaction.
     *                      It should include the customerID and other required fields.
     * @throws RuntimeException If an error occurs during the transaction process.
     * @throws InvalidArgumentException If the payload is invalid.
     * @return void
     */
    public function addNewTransaction(array $payload): void
    {
        try {
            TransactionsValidator::validateAddTransactionRequest($payload);

            $payload['id'] = $this->helperModel->generateUuid();
            $payload['customerID'] = $this->getCustomerIDFromToken();

            $response = $this->transactionModel->addNewTransaction($payload);

            if (!$response) {
                ResponseHelper::sendErrorResponse('Failed to add transaction', 400);
                exit;
            }

            ResponseHelper::sendSuccessResponse($response, 'Transaction added successfully', 201);
        } catch (RuntimeException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        } catch (InvalidArgumentException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Updates the status of a transaction.
     *
     * @param array $parameter An array containing the parameter for the transaction. It should include the 'id' key.
     * @param array $payload An array containing the payload for the transaction. It should include the 'status' key.
     * @throws RuntimeException If an error occurs during the transaction update process.
     * @throws InvalidArgumentException If the parameter or payload is invalid.
     * @return void
     */
    public function updateTransactionStatus(array $parameter, array $payload): void
    {

        try {
            TransactionsValidator::validateEditTransactionRequest($parameter, $payload);

            $transactionID = $parameter['id'];
            $status = $payload['status'];

            $response = $this->transactionModel->updateTransactionStatus($transactionID, $status);

            if (!$response) {
                ResponseHelper::sendErrorResponse('Failed to update transaction status', 400);
                exit;
            }

            ResponseHelper::sendSuccessResponse([], 'Transaction status updated successfully', 201);
        } catch (RuntimeException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        } catch (InvalidArgumentException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 400);
        }
    }

    public function updateTransactionOrderPurpose($params, $payload)
    {
        try {

            $transactionID = $params['id'];
            $status = $payload['status'];

            $response = $this->transactionModel->updateTransactionOrderPurpose($transactionID, $status);

            if (!$response) {
                ResponseHelper::sendErrorResponse('Failed to update transaction order purpose', 400);
                exit;
            }

            ResponseHelper::sendSuccessResponse([], 'Transaction order purpose updated successfully', 201);
        } catch (RuntimeException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        } catch (InvalidArgumentException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Retrieves the customer ID from the JWT token.
     *
     * This function validates the presence of a cookie, extracts the access token
     * from the cookie header, and decodes the JWT data to obtain the customer ID.
     *
     * @return int The customer ID extracted from the JWT token.
     */
    public function getCustomerIDFromToken()
    {
        $cookieHeader = $this->cookieManager->validateCookiePresence();
        $token = $this->cookieManager->extractAccessTokenFromCookieHeader($cookieHeader['cookie']);
        $decodedToken = (object) $this->jwt->decodeJWTData($token);

        return $decodedToken->id;
    }
}
