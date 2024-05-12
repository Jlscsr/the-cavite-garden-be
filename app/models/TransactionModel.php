<?php

namespace App\Models;

use PDO;

use RuntimeException;

use App\Models\ProductsModel;

class TransactionModel
{
    private $pdo;
    private $plantModel;

    private const TRANSACTION_TABLE = "transaction_tb";
    private const PRODUCT_TRANSACTION_TABLE = "product_transaction_tb";

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->plantModel = new ProductsModel($pdo);
    }

    /**
     * Retrieves all transactions from the database based on the provided status.
     *
     * @param string|array $status The status or array of statuses to filter transactions.
     * @return array An array containing the retrieved transaction data.
     * @throws RuntimeException If a product is not found during processing.
     */
    public function getAllTransactions(string | array $status): array
    {
        $query = "SELECT t.*, c.firstName, c.lastName FROM " . self::TRANSACTION_TABLE . " t JOIN customers_tb c ON t.customerID = c.id";

        if (is_array($status)) {
            $query .= " WHERE status IN (:successStatus, :failedStatus)";
        } else {
            $query .= " WHERE status = :pendingStatus";
        }

        $statement = $this->pdo->prepare($query);

        if (is_array($status)) {
            $statement->bindValue(':successStatus', $status[0], PDO::PARAM_STR);
            $statement->bindValue(':failedStatus', $status[1], PDO::PARAM_STR);
        } else {
            $statement->bindValue(':pendingStatus', $status, PDO::PARAM_STR);
        }

        try {
            $statement->execute();

            $transactionData = [];
            $productTransactionMap = [];

            while ($transactionValue = $statement->fetch(PDO::FETCH_ASSOC)) {
                $transactionID = $transactionValue['id'];
                $transactionData[] = $transactionValue;
                $productTransactionMap[$transactionID] = [];
            }

            $query = "SELECT productID, transactionID, productQuantity FROM " . self::PRODUCT_TRANSACTION_TABLE;
            $statement = $this->pdo->prepare($query);
            $statement->execute();

            while ($productTransactionValue = $statement->fetch(PDO::FETCH_ASSOC)) {
                $productTransactionMap[$productTransactionValue['transactionID']][] = $productTransactionValue;
            }

            foreach ($transactionData as $key => &$transactionValue) {
                $transactionID = $transactionValue['id'];
                $products = [];

                foreach ($productTransactionMap[$transactionID] as $productTransactionValue) {
                    $response = $this->plantModel->getProductByID((int) $productTransactionValue['productID']);

                    if (!$response) {
                        throw new RuntimeException("Product not found.");
                    }

                    $response['productQuantity'] = $productTransactionValue['productQuantity'];
                    $products[] = $response;
                }
                $transactionValue['productsPurchased'] = $products;
            }

            return $transactionData;
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage(), 500);
        }
    }

    public function getAllTransactionsByCustomerId()
    {
        //
    }

    /**
     * Adds a new transaction to the database based on the provided payload.
     *
     * @param array $payload An array containing the necessary data for the transaction.
     * @throws RuntimeException If an error occurs during the transaction process.
     * @return array The response data including customerID, transactionID, and status.
     */
    public function addNewTransaction(array $payload): array
    {
        $customerID = $payload['customerID'];
        $deliveryMethod = $payload['deliveryMethod'];
        $paymentMethod = $payload['paymentMethod'];
        $shippingAddress = $payload['shippingAddress'] ?? null;
        $purchasedProducts = $payload['purchasedProducts'];
        $status = "pending";
        $totalPrice = 0;

        foreach ($purchasedProducts as $key => $value) {
            $totalPrice += $value['productTotalPrice'];
        }

        $query = "INSERT INTO " . self::TRANSACTION_TABLE . " (customerID, deliveryMethod, paymentMethod, shippingAddress,totalPrice, status) VALUES (:customerID, :deliveryMethod, :paymentMethod, :shippingAddress, :totalPrice, :status)";
        $statement = $this->pdo->prepare($query);

        $statement->bindValue(':customerID', $customerID, PDO::PARAM_INT);
        $statement->bindValue(':deliveryMethod', $deliveryMethod, PDO::PARAM_STR);
        $statement->bindValue(':paymentMethod', $paymentMethod, PDO::PARAM_STR);
        $statement->bindValue(':shippingAddress', $shippingAddress, PDO::PARAM_STR);
        $statement->bindValue(':totalPrice', $totalPrice, PDO::PARAM_INT);
        $statement->bindValue(':status', $status, PDO::PARAM_STR);

        try {
            $statement->execute();

            if ($statement->rowCount() > 0) {
                $transactionID = (int) $this->pdo->lastInsertId();

                foreach ($purchasedProducts as $key => $value) {
                    $query = "INSERT INTO " . self::PRODUCT_TRANSACTION_TABLE . " (transactionID, productID, productQuantity, productTotalPrice) VALUES (:transactionID, :productID, :productQuantity, :productTotalPrice)";
                    $statement = $this->pdo->prepare($query);

                    $statement->bindValue(':transactionID', $transactionID, PDO::PARAM_INT);
                    $statement->bindValue(':productID', $value['productID'], PDO::PARAM_INT);
                    $statement->bindValue(':productQuantity', $value['productQuantity'], PDO::PARAM_INT);
                    $statement->bindValue(':productTotalPrice', $value['productTotalPrice'], PDO::PARAM_INT);

                    $statement->execute();
                }

                $query = "DELETE FROM cart_tb WHERE customerID = :customerID";
                $statement = $this->pdo->prepare($query);
                $statement->bindValue(':customerID', $customerID, PDO::PARAM_STR);

                $statement->execute();

                $response = [
                    'customerID' => $customerID,
                    'transactionID' => $transactionID,
                    'status' => $status
                ];

                return $response;
            }
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage(), 500);
        }
    }

    /**
     * Updates the status of a transaction in the database.
     *
     * @param int $transactionID The ID of the transaction to update.
     * @param string $status The new status to set for the transaction.
     * @throws RuntimeException If an error occurs during the update process.
     * @return bool Returns true if the update was successful, false otherwise.
     */
    public function updateTransactionStatus(int $transactionID, string $status): bool
    {

        $query = "UPDATE " . self::TRANSACTION_TABLE . " SET status = :status WHERE id = :id";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':id', $transactionID, PDO::PARAM_STR);
        $statement->bindValue(':status', $status, PDO::PARAM_STR);

        try {
            $statement->execute();
            return $statement->rowCount() > 0;
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage(), 500);
        }
    }
}
