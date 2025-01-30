<?php

namespace App\Controllers;

use InvalidArgumentException;
use RuntimeException;

use App\Models\RefundModel;

use App\Helpers\ResponseHelper;
use App\Models\HelperModel;

class RefundController
{
    private $refundModel;
    private $helperModel;


    public function __construct($pdo)
    {
        $this->refundModel = new RefundModel($pdo);
        $this->helperModel = new HelperModel($pdo);
    }

    public function getAllRefundTransactions(array $params)
    {
        try {
            $status = $params['status'] ?? 'all';

            $response = $this->refundModel->getAllRefundTransactions($status);

            if ($response['status'] === 'failed') {
                return ResponseHelper::sendSuccessResponse([], "No refund transactions found");
            }

            return ResponseHelper::sendSuccessResponse($response['data'], 'Refund transactions retrieved successfully');
        } catch (RuntimeException $e) {
            return ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }

    public function addNewRefundTransaction($payload)
    {
        try {
            $id = $this->helperModel->generateUuid();
            $payload['id'] = $id;
            $response = $this->refundModel->addNewRefundTransaction($payload);

            if ($response['status'] === 'failed') {
                return ResponseHelper::sendErrorResponse("Failed to add new refund transaction", 400);
            }

            return ResponseHelper::sendSuccessResponse($response['data'], 'New refund transaction added successfully');
        } catch (RuntimeException $e) {
            return ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }

    public function updateRefundTransactionStatus($params, $payload)
    {
        try {
            $id = $params['id'];
            $status = $payload['status'];
            $mediaURL = $payload['mediaURL'];

            $response = $this->refundModel->updateRefundTransactionStatus($id, $status, $mediaURL);

            if ($response['status'] === 'failed') {
                return ResponseHelper::sendSuccessResponse([], "Failed to update refund transaction status", 400);
            }

            return ResponseHelper::sendSuccessResponse([], 'Refund transaction status updated successfully');
        } catch (RuntimeException $e) {
            return ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }
}
