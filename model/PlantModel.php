<?php

namespace Models;

use Helpers\ResponseHelper;

use Models\CategoriesModel;
use Models\SubCategoriesModel;

use PDO;

class PlantModel
{
    private $pdo;
    private $categories_model;
    private $sub_categories_model;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->categories_model = new CategoriesModel($pdo);
        $this->sub_categories_model = new SubCategoriesModel($pdo);
    }

    /**
     * Retrieves all plants from the database.
     *
     * @return array An array of associative arrays representing the plants.
     */
    public function getAllPlants()
    {
        $query = "SELECT * FROM products_tb";
        $statement = $this->pdo->prepare($query);

        try {
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            ResponseHelper::sendDatabaseErrorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Retrieves all plants from the database that belong to a specific category.
     *
     * @param string $category_id The ID of the category.
     * @return array An array of associative arrays representing the plants.
     */
    public function getAllProductsByCategory($categoryID)
    {
        if (!$categoryID) {
            ResponseHelper::sendErrorResponse("Invalid category ID", 400);
            return;
        }

        $categoryID = (int) $categoryID;

        $query = "SELECT * FROM products_tb WHERE categoryId = :categoryId";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':categoryId', $categoryID, PDO::PARAM_STR);

        try {
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            ResponseHelper::sendDatabaseErrorResponse($e->getMessage(), 500);
        }
    }


    /**
     * Retrieves the details of a plant by its ID.
     *
     * @param int $plant_id The ID of the plant.
     * @return array An associative array containing the details of the plant, including the category and sub-category names.
     */
    public function getProductByID($productID)
    {
        if (!$productID) {
            ResponseHelper::sendErrorResponse("Invalid or missing product ID parameter", 400);
        }

        $productID = (int) $productID;

        $query = "SELECT * FROM products_tb WHERE id = :plantID";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':plantID', $productID, PDO::PARAM_STR);

        try {
            $statement->execute();

            if ($statement->rowCount() > 0) {
                $plantDetails = $statement->fetch(PDO::FETCH_ASSOC);

                $categoryID = $plantDetails['categoryId'];
                $subCategoryID = $plantDetails['subCategoryId'];

                $categoryName = $this->categories_model->getCategoriesColumnBy('name', 'id', $categoryID);
                $subCategoryName = $this->sub_categories_model->getSubCategoriesColumnBy('name', 'id', $subCategoryID);


                $plantDetails['category_name'] = $categoryName['name'] ?? null;
                $plantDetails['sub_category_name'] = $subCategoryName['name'] ?? null;

                return $plantDetails;
            }
        } catch (PDOException $e) {
            ResponseHelper::sendDatabaseErrorResponse($e->getMessage(), 500);
        }
    }


    /**
     * Adds a new plant to the products table based on the provided data.
     *
     * @param array $data An array containing the details of the new plant.
     * @throws PDOException If an error occurs during database operation.
     * @return bool Whether the plant was successfully added or not.
     */
    public function addNewProduct($payload)
    {

        if (!is_array($payload) && empty($payload)) {
            ResponseHelper::sendErrorResponse("Invalid payload or payload is empty", 400);
            return;
        }

        $productPhotoURL = $payload['product_photo_url'];
        $plantName = $payload['product_name'];
        $plantCategory = $payload['product_category'];
        $plantSubCategory = $payload['product_sub_category'] ?? '';
        $plantPrice = $payload['product_price'];
        $potSize = $payload['pot_size'] ?? '';
        $stock = $payload['stock'];
        $productDescription = $payload['product_description'];

        $categoryID = $this->categories_model->getCategoriesColumnBy('id', 'name', $plantCategory);
        $subCategoryId = null;

        if ($plantSubCategory !== '') {
            $subCategoryId = $this->sub_categories_model->getCategoriesColumnBy('id', 'name', $plantSubCategory);
        }

        if (!$subCategoryId) {
            $subCategoryId = null;
        }

        if ($categoryID === null) {
            ResponseHelper::sendErrorResponse("No category found", 400);
        }

        $query = "INSERT INTO products_tb (categoryId, subCategoryId, plant_name, plant_description, size, plant_image, plant_price, stock) VALUES (:category_id, :sub_category_id, :plant_name, :plant_description, :size, :plant_image, :plant_price, :stock)";
        $statement = $this->pdo->prepare($query);

        $bindParams = [
            ':category_id' => $categoryID,
            ':sub_category_id' => $subCategoryId,
            ':plant_name' => $plantName,
            ':plant_description' => $productDescription,
            ':size' => $potSize,
            ':plant_image' => $productPhotoURL,
            ':plant_price' => $plantPrice,
            ':stock' => $stock
        ];

        foreach ($bindParams as $value => $key) {
            $statement->bindValue($value, $key, PDO::PARAM_STR);
        }

        try {
            $statement->execute();

            return $statement->rowCount() > 0;
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Edits a plant in the database.
     *
     * @param string $id The ID of the plant to edit.
     * @param array $data An array containing the updated plant data.
     * @throws PDOException If there is an error executing the query.
     * @return bool Returns true if the plant was successfully edited, false otherwise.
     */
    public function editPlant($productID, $payload)
    {

        if (!is_array($payload) || empty($payload) || !$productID) {
            ResponseHelper::sendErrorResponse("Invalid data or data is empty", 400);
        }

        $productID = (int) $productID;

        $plantPhotoURL = $payload['product_photo_url'];
        $plantName = $payload['product_name'];
        $plantCategory = $payload['product_category'];
        $plantSubCategory = $payload['product_sub_category'] ?? '';
        $plantPrice = $payload['product_price'];
        $size = $payload['pot_size'] ?? '';
        $productDescription = $payload['product_description'];

        $categoryID = $this->categories_model->getCategoryByName($plantCategory);
        $categoryID = $categoryID[0]['id'];

        $subCategoryID = $this->sub_categories_model->getSubCategoryByName($plantSubCategory);
        $subCategoryID = $subCategoryID[0]['id'];

        $update_plant_query = "UPDATE products_tb SET categoryId = :categoryID, subCategoryId = :subCategoryID, plantName = :plantName, plant_description = :productDescription, size = :size, plant_image = :plantImage, plantPrice = :plantPrice WHERE id = :id";
        $update_plant_query = $this->pdo->prepare($update_plant_query);
        $update_plant_query->bindValue(':categoryID', $categoryID, PDO::PARAM_STR);
        $update_plant_query->bindValue(':subCategoryID', $subCategoryID, PDO::PARAM_STR);
        $update_plant_query->bindValue(':plantName', $plantName, PDO::PARAM_STR);
        $update_plant_query->bindValue(':productDescription', $productDescription, PDO::PARAM_STR);
        $update_plant_query->bindValue(':size', $size, PDO::PARAM_STR);
        $update_plant_query->bindValue(':plantImage', $plantPhotoURL, PDO::PARAM_STR);
        $update_plant_query->bindValue(':plantPrice', $plantPrice, PDO::PARAM_STR);
        $update_plant_query->bindValue(':id', $productID, PDO::PARAM_STR);

        try {
            $update_plant_query->execute();

            return $update_plant_query->rowCount() > 0;
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Deletes a plant from the database based on the given ID.
     *
     * @param string $id The ID of the plant to be deleted.
     * @return bool Returns true if the plant was successfully deleted, false otherwise.
     * @throws PDOException If there is an error executing the query.
     */
    public function deletePlant($productID)
    {
        if (!$productID) {
            ResponseHelper::sendErrorResponse("Invalid or missing product ID parameter", 400);
        }

        $productID = (int) $productID;

        $delete_query = "DELETE FROM products_tb WHERE id = :id";
        $delete_query = $this->pdo->prepare($delete_query);
        $delete_query->bindValue(':id', $productID, PDO::PARAM_STR);

        try {
            $delete_query->execute();
            return $delete_query->rowCount() > 0;
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }
}
