<?php

namespace App\Models;

use PDO;
use PDOException;

use RuntimeException;

use App\Models\ProductsModel;
use App\Models\HelperModel;

class TransactionModel
{
    private $pdo;
    private $plantModel;
    private $helperModel;

    private const ORDERS_TB = "orders_tb";
    private const ORDER_PRODUCTS_TB = "order_products_tb";

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->plantModel = new ProductsModel($pdo);
        $this->helperModel = new HelperModel($pdo);
    }

    /**
     * Retrieves all transactions from the database based on the provided status.
     *
     * @param string|array $status The status or array of statuses to filter transactions.
     * @return array An array containing the retrieved transaction data.
     * @throws RuntimeException If a product is not found during processing.
     */
    public function getAllTransactions(string $status)
    {
        try {
            $query = "";

            if ($status == "all") {
                $query = "SELECT * FROM " . self::ORDERS_TB . " ORDER BY createdAt DESC";
            } else {
                $query = "SELECT * FROM " . self::ORDERS_TB . " WHERE status = :status ORDER BY createdAt DESC";
            }

            // Fetch orders based on status
            $stmt = $this->pdo->prepare($query);

            if ($status != "all") {
                $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            }

            $stmt->execute();

            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $allTransactions = [];
            foreach ($orders as $order) {
                $orderID = $order['id'];

                // Fetch products associated with the order
                $productQuery = "SELECT * FROM " . self::ORDER_PRODUCTS_TB . " WHERE orderID = :orderID";
                $productStmt = $this->pdo->prepare($productQuery);
                $productStmt->bindParam(':orderID', $orderID, PDO::PARAM_STR);
                $productStmt->execute();

                $products = $productStmt->fetchAll(PDO::FETCH_ASSOC);

                // Loop through products and fetch full product info
                foreach ($products as &$product) {
                    $productID = $product['productID'];

                    // Query to get all product info
                    $productQuery = "SELECT * FROM products_tb WHERE id = :productID";
                    $productStmt = $this->pdo->prepare($productQuery);
                    $productStmt->bindParam(':productID', $productID, PDO::PARAM_STR);
                    $productStmt->execute();

                    $productInfo = $productStmt->fetch(PDO::FETCH_ASSOC);

                    // Add product info to product
                    $product['productInfo'] = $productInfo ? $productInfo : null;
                }

                // Fetch customer information for the order
                $customerQuery = "SELECT * FROM customers_tb WHERE id = :customerID";
                $customerStmt = $this->pdo->prepare($customerQuery);
                $customerStmt->bindParam(':customerID', $order['customerID'], PDO::PARAM_STR);
                $customerStmt->execute();

                $customerInfo = $customerStmt->fetch(PDO::FETCH_ASSOC);

                if ($customerInfo) {
                    // Fetch all shipping addresses for the customer
                    $addressQuery = "SELECT * FROM customers_ship_address_tb WHERE customerID = :customerID";
                    $addressStmt = $this->pdo->prepare($addressQuery);
                    $addressStmt->bindParam(':customerID', $order['customerID'], PDO::PARAM_STR);
                    $addressStmt->execute();

                    $shippingAddresses = $addressStmt->fetchAll(PDO::FETCH_ASSOC);

                    // Add shipping addresses to customerInfo
                    $customerInfo['shippingAddresses'] = $shippingAddresses;
                }

                // Add customer info to the order
                $order['customerInfo'] = $customerInfo ? $customerInfo : null;

                // Add products to the order
                $order['products'] = $products;

                // Add the order to the transactions list
                $allTransactions[] = $order;
            }

            return $allTransactions;
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage(), 500);
        }
    }



    public function getTransactionByCustomerID(string $customerID)
    {
        try {
            // Fetch orders for the given customer
            $query = "SELECT * FROM " . self::ORDERS_TB . " WHERE customerID = :customerID ORDER BY createdAt DESC";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':customerID', $customerID, PDO::PARAM_STR);
            $stmt->execute();

            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $allTransactions = [];
            foreach ($orders as $order) {
                $orderID = $order['id'];

                // Fetch products associated with the order
                $productQuery = "SELECT * FROM " . self::ORDER_PRODUCTS_TB . " WHERE orderID = :orderID";
                $productStmt = $this->pdo->prepare($productQuery);
                $productStmt->bindParam(':orderID', $orderID, PDO::PARAM_STR);
                $productStmt->execute();

                $products = $productStmt->fetchAll(PDO::FETCH_ASSOC);

                // Loop through products and fetch productName
                foreach ($products as &$product) {  // Pass by reference
                    $productID = $product['productID'];

                    // Query to get product info (productName)
                    $productQuery = "SELECT id, productName FROM products_tb WHERE id = :productID";
                    $productStmt = $this->pdo->prepare($productQuery);
                    $productStmt->bindParam(':productID', $productID, PDO::PARAM_STR);
                    $productStmt->execute();

                    $productInfo = $productStmt->fetch(PDO::FETCH_ASSOC);

                    // If product info exists, add productName to the product
                    if ($productInfo) {
                        $product['productInfo'] = $productInfo;
                    } else {
                        // In case product info is not found, you can set a default value
                        $product['productInfo'] = [];
                    }
                }

                // Add the updated products to the order
                $order['products'] = $products;

                // Add the order (with products having productName) to the transactions list
                $allTransactions[] = $order;
            }

            return $allTransactions;
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage(), 500);
        }
    }




    /**
     * Adds a new transaction to the database based on the provided payload.
     *
     * @param array $payload An array containing the necessary data for the transaction.
     * @throws RuntimeException If an error occurs during the transaction process.
     * @return array The response data including customerID, transactionID, and status.
     */
    public function addNewTransaction(array $payload)
    {
        $id = $payload['id'];
        $customerID = $payload['customerID'];
        $orderType = $payload['orderType'];
        $paymentType = $payload['paymentType'];
        $shippingAddress = $payload['shippingAddress'] ?? null;
        $status = $payload['status'];

        $query = "INSERT INTO " . self::ORDERS_TB . " (id, customerID, orderType, paymentType, shippingAddress, status) VALUES (:id, :customerID, :orderType, :paymentType, :shippingAddress, :status)";

        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_STR);
        $statement->bindValue(':customerID', $customerID, PDO::PARAM_STR);
        $statement->bindValue(':orderType', $orderType, PDO::PARAM_STR);
        $statement->bindValue(':paymentType', $paymentType, PDO::PARAM_STR);
        $statement->bindValue(':shippingAddress', $shippingAddress, PDO::PARAM_STR);
        $statement->bindValue(':status', $status, PDO::PARAM_STR);

        try {
            $statement->execute();

            if ($statement->rowCount() > 0) {
                $purchasedProducts = $payload['purchasedProducts'];
                $orderID = $id;

                foreach ($purchasedProducts as $key => $value) {
                    $id = $this->helperModel->generateUuid();
                    $query = "INSERT INTO " . self::ORDER_PRODUCTS_TB . " (id, orderID, productID, purchasedQuantity, productPrice, totalPrice) VALUES (:id, :orderID, :productID, :purchasedQuantity, :productPrice, :totalPrice)";
                    $statement = $this->pdo->prepare($query);

                    $statement->bindValue(":id", $id, PDO::PARAM_STR);
                    $statement->bindValue(':orderID', $orderID, PDO::PARAM_STR);
                    $statement->bindValue(':productID', $value['productInfo']['id'], PDO::PARAM_STR);
                    $statement->bindValue(':purchasedQuantity', $value['productQuantity'], PDO::PARAM_INT);
                    $statement->bindValue(':productPrice', $value['productInitialPrice'], PDO::PARAM_INT);
                    $statement->bindValue(':totalPrice', $value['totalPrice'], PDO::PARAM_INT);

                    $statement->execute();
                }

                // Deduct the quantity of the purchased products from the product table

                foreach ($purchasedProducts as $key => $value) {
                    $productID = $value['productInfo']['id'];
                    $purchasedQuantity = $value['productQuantity'];

                    $query = "UPDATE products_tb SET productStock = productStock - :purchasedQuantity WHERE id = :productID";

                    $statement = $this->pdo->prepare($query);

                    $statement->bindValue(':productID', $productID, PDO::PARAM_STR);

                    $statement->bindValue(':purchasedQuantity', $purchasedQuantity, PDO::PARAM_STR);

                    $statement->execute();

                    if ($statement->rowCount() <= 0) {
                        throw new RuntimeException("Failed to update product stock.");
                    }
                }

                $query = "DELETE FROM customer_cart_tb WHERE customerID = :customerID";
                $statement = $this->pdo->prepare($query);
                $statement->bindValue(':customerID', $customerID, PDO::PARAM_STR);

                $statement->execute();

                $response = [
                    'customerID' => $customerID,
                    'orderID' => $orderID,
                    'status' => $status,
                    'orderType' => $orderType,
                    'paymentType' => $paymentType,
                    'shippingAddress' => $shippingAddress,
                    'purchasedProducts' => $purchasedProducts
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
    public function updateTransactionStatus(string $transactionID, string $status): bool
    {

        $query = "UPDATE " . self::ORDERS_TB . " SET status = :status WHERE id = :id";
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
