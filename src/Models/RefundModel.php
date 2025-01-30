<?php

namespace App\Models;

use PDO;
use PDOException;
use RuntimeException;

use App\Models\HelperModel;

class RefundModel
{
    private $pdo;
    private $helperModel;

    private const REFUND_TRANSACTIONS_TABLE = 'refund_transactions_tb';
    private const REFUND_TRANSACTIONS_MEDIA_TABLE = 'refund_transactions_media_tb';
    private const PRODUCTS_TABLE = 'products_tb';
    private const CUSTOMERS_TABLE = 'customers_tb';

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->helperModel = new HelperModel($pdo);
    }

    public function getAllRefundTransactions(string $status)
    {
        try {

            if ($status === 'all') {
                $query = "SELECT * FROM " . self::REFUND_TRANSACTIONS_TABLE;
            } else {
                $query = "SELECT * FROM " . self::REFUND_TRANSACTIONS_TABLE . " WHERE status = :status";
            }

            $stmt = $this->pdo->prepare($query);

            if ($status !== 'all') {
                $stmt->bindParam(':status', $status);
            }

            $stmt->execute();

            $refundTransactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($refundTransactions as $key => $refundTransaction) {
                $userQuery = "SELECT * FROM " . self::CUSTOMERS_TABLE . " WHERE id = :userID";
                $userStmt = $this->pdo->prepare($userQuery);
                $userStmt->bindValue(':userID', $refundTransaction['userID'], PDO::PARAM_STR);
                $userStmt->execute();

                $refundTransactions[$key]['customerInfo'] = $userStmt->fetch(PDO::FETCH_ASSOC);
            }

            foreach ($refundTransactions as $key => $refundTransaction) {
                $productQuery = "SELECT * FROM " . self::PRODUCTS_TABLE . " WHERE id = :productID";

                $productStmt = $this->pdo->prepare($productQuery);
                $productStmt->bindValue(':productID', $refundTransaction['productID'], PDO::PARAM_STR);

                $productStmt->execute();

                $refundTransactions[$key]['productInfo'] = $productStmt->fetch(PDO::FETCH_ASSOC);
            }

            foreach ($refundTransactions as $key => $refundTransaction) {
                $mediaQuery = "SELECT * FROM " . self::REFUND_TRANSACTIONS_MEDIA_TABLE . " WHERE refundID = :refundID";
                $mediaStmt = $this->pdo->prepare($mediaQuery);
                $mediaStmt->bindValue(':refundID', $refundTransaction['id'], PDO::PARAM_STR);
                $mediaStmt->execute();

                $refundTransactions[$key]['medias'] = $mediaStmt->fetchAll(PDO::FETCH_ASSOC);
            }

            if (empty($refundTransactions)) {
                return [
                    'status' => 'failed',
                    'data' => []
                ];
            }

            return [
                'status' => 'success',
                'data' => $refundTransactions
            ];
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    public function addNewRefundTransaction(array $payload)
    {
        try {
            $refundID = $payload['id'];
            $userID = $payload['userID'];
            $productID = $payload['productID'];
            $contactDetails = $payload['contactDetails'];
            $productQuantity = $payload['productQuantity'];
            $productPrice = $payload['productPrice'];
            $totalPrice = $payload['totalPrice'];
            $refundReason = $payload['refundReason'];
            $paymentMethod = $payload['paymentMethod'];
            $gcashNumber = $payload['gcashNumber'];
            $status = $payload['status'];


            $query = "INSERT INTO " . self::REFUND_TRANSACTIONS_TABLE . " (id, userID, productID, contactDetails, productQuantity, productPrice, totalPrice, paymentMethod, gcashNumber, refundReason, status) VALUES (:id, :userID, :productID, :contactDetails, :productQuantity, :productPrice, :totalPrice, :paymentMethod, :gcashNumber, :refundReason, :status)";

            $stmt = $this->pdo->prepare($query);

            $bindParams = [
                ':id' => $refundID,
                ':userID' => $userID,
                ':productID' => $productID,
                ':contactDetails' => $contactDetails,
                ':productQuantity' => $productQuantity,
                ':productPrice' => $productPrice,
                ':totalPrice' => $totalPrice,
                ':paymentMethod' => $paymentMethod,
                ':gcashNumber' => $gcashNumber,
                ':refundReason' => $refundReason,
                ':status' => $status
            ];

            foreach ($bindParams as $key => $value) {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }

            $stmt->execute();

            // medias is array
            foreach ($payload['mediasRefund'] as $media) {
                $id = $this->helperModel->generateUuid();
                $mediaQuery = "
                    INSERT INTO " . self::REFUND_TRANSACTIONS_MEDIA_TABLE . " (id, refundID, mediaURL, mediaType)
                    VALUES (:id, :refundID, :mediaURL, :mediaType)
                ";
                $mediaStmt = $this->pdo->prepare($mediaQuery);

                $mediaStmt->bindValue(':id', $id, PDO::PARAM_STR);
                $mediaStmt->bindValue(':refundID', $refundID, PDO::PARAM_STR);
                $mediaStmt->bindValue(':mediaURL', $media['mediaURL'], PDO::PARAM_STR);
                $mediaStmt->bindValue(':mediaType', $media['mediaType'], PDO::PARAM_STR);

                $mediaStmt->execute();

                if ($stmt->rowCount() === 0) {
                    return [
                        'status' => 'failed',
                        'message' => 'Failed to add new refund transaction'
                    ];
                }

                return [
                    'status' => 'success',
                    'message' => 'Refund transaction added successfully',
                    'data' => ['id' => $refundID]
                ];
            }
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    public function updateRefundTransactionStatus(string $id, string $status, string $mediaURL)
    {
        try {
            $query = "UPDATE " . self::REFUND_TRANSACTIONS_TABLE . " SET status = :status WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                return [
                    'status' => 'failed',
                    'message' => 'Failed to update refund transaction status'
                ];
            }

            if ($mediaURL !== 'n/a') {
                $mediaQuery = "UPDATE " . self::REFUND_TRANSACTIONS_MEDIA_TABLE . " SET refundProofMediaURL = :mediaURL WHERE refundID = :refundID";
                $mediaStmt = $this->pdo->prepare($mediaQuery);
                $mediaStmt->bindValue(':mediaURL', $mediaURL);
                $mediaStmt->bindValue(':refundID', $id);
                $mediaStmt->execute();

                if ($mediaStmt->rowCount() === 0) {
                    return [
                        'status' => 'failed',
                        'message' => 'Failed to update refund transaction status'
                    ];
                }
            }

            return [
                'status' => 'success',
                'message' => 'Refund transaction status updated successfully'
            ];
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }
}
