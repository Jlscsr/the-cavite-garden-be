<?php

namespace App\Controllers;

use InvalidArgumentException;
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


    public function getCustomerIDFromToken()
    {
        $cookieHeader = $this->cookieManager->validateCookiePressence();
        $response = $this->cookieManager->extractAccessTokenFromCookieHeader($cookieHeader);
        $decodedToken = (object) $this->jwt->decodeJWTData($response['token']);

        return $decodedToken->id;
    }
}
