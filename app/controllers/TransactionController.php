<?php

use App\Models\TransactionModel;

use App\Validators\TransactionsValidator;

use App\Helpers\JWTHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\CookieManager;

class TransactionController
{
    private $jwt;
    private $transactionModel;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->jwt = new JWTHelper();
        $this->transactionModel = new TransactionModel($pdo);
        $this->cookieManager = new CookieManager();
    }

    /**
     * Retrieves all transactions based on the provided parameter.
     *
     * @param array $parameter An array containing the parameters for filtering transactions.
     * @throws RuntimeException If an error occurs during the transaction retrieval process.
     * @return void
     */
    public function getAllTransactions(array $parameter): void
    {
        try {
            TransactionsValidator::validateGetTransactionsByParameter($parameter);

            $status = explode("-", $parameter['status'] ?? '');
            $status = count($status) === 2 ? $status : $parameter['status'];

            $response = $this->transactionModel->getAllTransactions($status);

            if (!$response) {
                ResponseHelper::sendErrorResponse('No Transactions found', 404);
                exit;
            }

            ResponseHelper::sendSuccessResponse($response, 'Successfully fetched transactions', 200);
        } catch (RuntimeException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        } catch (InvalidArgumentException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 400);
        }
    }

    public function getTransactionByCustomerID($parameter): void
    {
        try {
            $response = $this->transactionModel->getTransactionByCustomerID($parameter['id']);

            if (!$response) {
                ResponseHelper::sendErrorResponse('No Transactions found', 404);
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

    /**
     * Retrieves the customer ID from the JWT token.
     *
     * This function validates the presence of a cookie, extracts the access token
     * from the cookie header, and decodes the JWT data to obtain the customer ID.
     *
     * @return int The customer ID extracted from the JWT token.
     */
    public function getCustomerIDFromToken(): int
    {
        $cookieHeader = $this->cookieManager->validateCookiePressence();
        $response = $this->cookieManager->extractAccessTokenFromCookieHeader($cookieHeader);
        $decodedToken = $this->jwt->decodeJWTData($response['token']);

        return $decodedToken->id;
    }
}
