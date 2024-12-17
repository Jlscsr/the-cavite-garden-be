<?php

namespace App\Controllers;

use RuntimeException;

use App\Models\ReviewsModel;

use App\Helpers\JWTHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\CookieManager;
use App\Models\HelperModel;

class ReviewsController
{
    private $jwt;
    private $reviewsModel;
    private $cookieManager;
    private $helperModel;

    public function __construct($pdo)
    {
        $this->jwt = new JWTHelper();
        $this->reviewsModel = new ReviewsModel($pdo);
        $this->cookieManager = new CookieManager();
        $this->helperModel = new HelperModel($pdo);
    }

    public function getAllReviews()
    {
        try {
            $response = $this->reviewsModel->getAllReviews();

            if ($response['status'] === 'success' && !isset($response['data'])) {
                ResponseHelper::sendSuccessResponse([], 'No reviews found');
                exit;
            }

            ResponseHelper::sendSuccessResponse($response['data'], $response['message'], 200);
        } catch (RuntimeException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }

    public function addNewProductReview(array $payload)
    {
        try {
            $payload['id'] = $this->helperModel->generateUuid();
            $payload['customerID'] = $this->getCustomerIDFromToken();

            $response = $this->reviewsModel->addNewProductReview($payload);

            if (!$response) {
                ResponseHelper::sendErrorResponse('Failed to add review', 500);
                exit;
            }

            ResponseHelper::sendSuccessResponse([], 'Review added successfully', 201);
        } catch (RuntimeException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }

    public function addReviewReply(array $payload)
    {
        try {
            $payload['id'] = $this->helperModel->generateUuid();
            $response = $this->reviewsModel->addReviewReply($payload);

            if ($response['status'] === 'failed') {
                ResponseHelper::sendErrorResponse('Failed to add review reply', 500);
                exit;
            }

            ResponseHelper::sendSuccessResponse([], $response['message'], 201);
        } catch (RuntimeException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }

    public function deleteReview(array $params)
    {
        try {
            $response = $this->reviewsModel->deleteReview($params['id']);

            if ($response['status'] === 'failed') {
                ResponseHelper::sendErrorResponse('Failed to delete review', 400);
                exit;
            }

            ResponseHelper::sendSuccessResponse([], 'Review deleted successfully', 200);
        } catch (RuntimeException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }

    public function getCustomerIDFromToken()
    {
        $cookieHeader = $this->cookieManager->validateCookiePresence();
        $token = $this->cookieManager->extractAccessTokenFromCookieHeader($cookieHeader['cookie']);
        $decodedToken = (object) $this->jwt->decodeJWTData($token);

        return $decodedToken->id;
    }
}
