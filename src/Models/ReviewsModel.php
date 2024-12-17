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
    private const PRODUCTS_TB = "products_tb";
    private const CUSTOMERS_TB = "customers_tb";
    private const PRODUCT_REVIEWS_REPLY_TB = "product_reviews_reply_tb";

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->helperModel = new HelperModel($pdo);
    }

    public function getAllReviews()
    {
        try {
            $query = "SELECT * FROM " . self::PRODUCT_REVIEWS_TB;
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();

            $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($reviews)) {
                return ['status' => 'success', 'message' => 'No reviews found'];
            }

            // get the associated user

            foreach ($reviews as $key => $review) {
                $userQuery = "SELECT * FROM " . self::CUSTOMERS_TB . " WHERE id = :userID";
                $userStmt = $this->pdo->prepare($userQuery);
                $userStmt->bindValue(':userID', $review['userID'], PDO::PARAM_STR);
                $userStmt->execute();

                $reviews[$key]['user'] = $userStmt->fetch(PDO::FETCH_ASSOC);
            }

            foreach ($reviews as $key => $review) {
                $productQuery = "SELECT * FROM " . self::PRODUCTS_TB . " WHERE id = :productID";
                $productStmt = $this->pdo->prepare($productQuery);
                $productStmt->bindValue(':productID', $review['productID'], PDO::PARAM_STR);
                $productStmt->execute();

                $reviews[$key]['product'] = $productStmt->fetch(PDO::FETCH_ASSOC);
            }

            foreach ($reviews as $key => $review) {
                $mediaQuery = "SELECT * FROM " . self::PRODUCT_REVIEWS_MEDIA_TB . " WHERE productReviewID = :productReviewID";
                $mediaStmt = $this->pdo->prepare($mediaQuery);
                $mediaStmt->bindValue(':productReviewID', $review['id'], PDO::PARAM_STR);
                $mediaStmt->execute();

                $reviews[$key]['medias'] = $mediaStmt->fetchAll(PDO::FETCH_ASSOC);
            }

            return ['status' => 'success', 'data' => $reviews, 'message' => 'Reviews fetched successfully'];
        } catch (PDOException $e) {
            throw new RuntimeException("Database Error: " . $e->getMessage());
        }
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

    public function addReviewReply(array $payload)
    {
        try {
            $reviewQuery = "
                INSERT INTO " . self::PRODUCT_REVIEWS_REPLY_TB . " (id, productReviewID, replyComment)
                VALUES (:id, :productReviewID, :replyComment)
            ";
            $reviewStmt = $this->pdo->prepare($reviewQuery);

            $reviewStmt->bindValue(':id', $payload['id'], PDO::PARAM_STR);
            $reviewStmt->bindValue(':productReviewID', $payload['productReviewID'], PDO::PARAM_STR);
            $reviewStmt->bindValue(':replyComment', $payload['replyComment'], PDO::PARAM_STR);

            $reviewStmt->execute();

            if ($reviewStmt->rowCount() === 0) {
                return ['status' => 'failed', 'message' => 'Failed to add review reply'];
            }

            return ['status' => 'success', 'message' => 'Review reply added successfully'];
        } catch (PDOException $e) {
            throw new RuntimeException("Database Error: " . $e->getMessage());
        }
    }

    public function deleteReview($id)
    {
        try {
            // Delete first the media associated with the review

            $mediaQuery = "DELETE FROM " . self::PRODUCT_REVIEWS_MEDIA_TB . " WHERE productReviewID = :productReviewID";

            $mediaStmt = $this->pdo->prepare($mediaQuery);
            $mediaStmt->bindValue(':productReviewID', $id, PDO::PARAM_STR);

            $mediaStmt->execute();

            // Delete the review

            $reviewQuery = "DELETE FROM " . self::PRODUCT_REVIEWS_TB . " WHERE id = :id";

            $reviewStmt = $this->pdo->prepare($reviewQuery);

            $reviewStmt->bindValue(':id', $id, PDO::PARAM_STR);

            $reviewStmt->execute();

            if ($reviewStmt->rowCount() === 0) {
                return ['status' => 'failed', 'message' => 'Failed to delete review'];
            }

            return ['status' => 'success', 'message' => 'Review deleted successfully'];
        } catch (PDOException $e) {
            throw new RuntimeException("Database Error: " . $e->getMessage());
        }
    }
}
