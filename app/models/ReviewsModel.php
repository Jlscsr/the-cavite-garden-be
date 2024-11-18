<?php

namespace App\Models;

use PDO;
use PDOException;
use RuntimeException;

use App\Models\HelperModel;

class ReviewsModel
{
    private $pdo;
    private $helperModel;

    private const PRODUCT_REVIEWS_TB = "product_reviews_tb";
    private const PRODUCT_REVIEWS_MEDIA_TB = "product_reviews_media_tb";

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->helperModel = new HelperModel($pdo);
    }

    public function addNewProductReview(array $payload)
    {
        try {
            $reviewQuery = "
                INSERT INTO " . self::PRODUCT_REVIEWS_TB . " (id, userID, productID, userComment, userRating)
                VALUES (:id, :userID, :productID, :userComment, :userRating)
            ";
            $reviewStmt = $this->pdo->prepare($reviewQuery);

            $reviewStmt->bindValue(':id', $payload['id'], PDO::PARAM_STR);
            $reviewStmt->bindValue(':userID', $payload['customerID'], PDO::PARAM_STR);
            $reviewStmt->bindValue(':productID', $payload['productID'], PDO::PARAM_STR);
            $reviewStmt->bindValue(':userComment', $payload['userComment'], PDO::PARAM_STR);
            $reviewStmt->bindValue(':userRating', $payload['userRating'], PDO::PARAM_INT);

            $reviewStmt->execute();

            // Get the last inserted ID for the review
            $reviewID = $payload['id'];

            // medias is array
            foreach ($payload['mediasReview'] as $media) {
                $id = $this->helperModel->generateUuid();
                $mediaQuery = "
                    INSERT INTO " . self::PRODUCT_REVIEWS_MEDIA_TB . " (id, productReviewID, mediaURL, mediaType)
                    VALUES (:id, :reviewID, :mediaURL, :mediaType)
                ";
                $mediaStmt = $this->pdo->prepare($mediaQuery);

                $mediaStmt->bindValue(':id', $id, PDO::PARAM_STR);
                $mediaStmt->bindValue(':reviewID', $reviewID, PDO::PARAM_STR);
                $mediaStmt->bindValue(':mediaURL', $media['mediaURL'], PDO::PARAM_STR);
                $mediaStmt->bindValue(':mediaType', $media['mediaType'], PDO::PARAM_STR);

                $mediaStmt->execute();


                return $mediaStmt->rowCount() > 0;
            }
        } catch (PDOException $e) {
            throw new RuntimeException("Database Error: " . $e->getMessage());
        }
    }
}
