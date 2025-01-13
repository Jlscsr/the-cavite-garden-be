<?php

namespace App\Controllers;

use InvalidArgumentException;
use RuntimeException;

use App\Models\ReportsModel;

use App\Helpers\ResponseHelper;


class ReportsController
{
    private $reportsModel;


    public function __construct($pdo)
    {
        $this->reportsModel = new ReportsModel($pdo);
    }

    public function getAllReports($payload)
    {
        try {
            $startDate = $payload['startDate'] ?? 'n/a';
            $endDate = $payload['endDate'] ?? 'n/a';

            $response = $this->reportsModel->getAllReports($startDate, $endDate);

            if ($response['status'] === 'failed') {
                return ResponseHelper::sendSuccessResponse([], "No reports found", 404);
            }

            return ResponseHelper::sendSuccessResponse($response['data'], 'Reports retrieved successfully');
        } catch (RuntimeException $e) {
            return ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }
}
