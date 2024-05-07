<?php

namespace Models;

use Models\CategoriesModel;
use Models\SubCategoriesModel;

use PDO;

use InvalidArgumentException;
use RuntimeException;

class ProductsModel
{
    private $pdo;
    private $categoriesModel;
    private $subCategoriesModel;

    private const PRODUCTS_TABLE = 'products_tb';

    /**
     * Constructs a new instance of the class.
     *
     * @param PDO $pdo The PDO object for database connection.
     * @param CategoriesModel $categoriesModel The instance of the CategoriesModel class.
     * @param SubCategoriesModel $subCategoriesModel The instance of the SubCategoriesModel class.
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->categoriesModel = new CategoriesModel($pdo);
        $this->subCategoriesModel = new SubCategoriesModel($pdo);
    }


    /**
     * Retrieves all products from the database.
     *
     * @throws RuntimeException if there is a database error
     * @return array an array of associative arrays representing the products
     */
    public function getAllProducts()
    {
        $query = "SELECT * FROM " . self::PRODUCTS_TABLE;
        $statement = $this->pdo->prepare($query);

        try {
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new RuntimeException("Database Error: " . $e->getMessage());
        }
    }


    /**
     * Retrieves a product from the database by its ID.
     *
     * @param int $productID The ID of the product to retrieve.
     * @throws InvalidArgumentException If the product ID is invalid or missing.
     * @throws RuntimeException If the product is not found or there is a database error.
     * @return array An associative array representing the product details, including the category and subcategory names.
     */
    public function getProductByID($productID)
    {
        if (!$productID) {
            throw new InvalidArgumentException('Invalid or missing product ID parameter');
        }

        $query = "SELECT * FROM " . self::PRODUCTS_TABLE . " WHERE id = :plantID";

        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':plantID', $productID, PDO::PARAM_STR);

        try {
            $statement->execute();

            if (!$statement->rowCount() > 0) {
                throw new RuntimeException("Products not found");
            }

            $productDetails = $statement->fetch(PDO::FETCH_ASSOC);

            $categoryID = $productDetails['categoryId'];
            $subCategoryID = $productDetails['subCategoryId'];

            $categoryName = $this->categoriesModel->getCategoriesColumnBy('name', 'id', $categoryID);
            $subCategoryName = $this->subCategoriesModel->getSubCategoriesColumnBy('name', 'id', $subCategoryID);


            $productDetails['category_name'] = $categoryName['name'] ?? null;
            $productDetails['sub_category_name'] = $subCategoryName['name'] ?? null;

            return $productDetails;
        } catch (PDOException $e) {
            throw new RuntimeException("Database Error: " . $e->getMessage());
        }
    }


    /**
     * Retrieves all products from the database by category ID.
     *
     * @param int $categoryID The ID of the category to retrieve products for.
     * @throws InvalidArgumentException If the category ID is invalid or missing.
     * @throws RuntimeException If there is a database error.
     * @return array An array of associative arrays representing the products.
     */
    public function getAllProductsByCategory($categoryID)
    {
        if (!$categoryID) {
            throw new InvalidArgumentException('Invalid or missing category ID parameter');
        }

        $query = "SELECT * FROM " . self::PRODUCTS_TABLE . " WHERE categoryId = :categoryId";

        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':categoryId', $categoryID, PDO::PARAM_STR);

        try {
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new RuntimeException("Database Error: " . $e->getMessage());
        }
    }


    /**
     * Adds a new product to the database.
     *
     * @param array $payload The payload containing the product details.
     * @throws InvalidArgumentException If the payload is invalid or empty.
     * @throws RuntimeException If the category is not found.
     * @return bool Returns true if the product is successfully added, false otherwise.
     */
    public function addNewProduct($payload)
    {

        if (!is_array($payload) && empty($payload)) {
            throw new InvalidArgumentException("Invalid payload or payload is empty");
        }

        $productPhotoURL = $payload['product_photo_url'];
        $productName = $payload['product_name'];
        $productCategory = $payload['product_category'];
        $productSubCategory = $payload['product_sub_category'] ?? '';
        $productPrice = $payload['product_price'];
        $productSize = $payload['pot_size'] ?? '';
        $stock = $payload['stock'];
        $productDescription = $payload['product_description'];

        $categoryID = $this->categoriesModel->getCategoriesColumnBy('id', 'name', $productCategory);
        $subCategoryId = null;

        if ($productSubCategory !== '') {
            $subCategoryId = $this->subCategoriesModel->getCategoriesColumnBy('id', 'name', $productSubCategory);
        }

        if (!$subCategoryId) {
            $subCategoryId = null;
        }

        if ($categoryID === null) {
            throw new RuntimeException("Category not found");
            return;
        }

        $query = "INSERT INTO " . self::PRODUCTS_TABLE . " (categoryId, subCategoryId, plant_name, plant_description, size, plant_image, plant_price, stock) VALUES (:category_id, :sub_category_id, :plant_name, :plant_description, :size, :plant_image, :plant_price, :stock)";
        $statement = $this->pdo->prepare($query);

        $bindParams = [
            ':category_id' => $categoryID,
            ':sub_category_id' => $subCategoryId,
            ':plant_name' => $productName,
            ':plant_description' => $productDescription,
            ':size' => $productSize,
            ':plant_image' => $productPhotoURL,
            ':plant_price' => $productPrice,
            ':stock' => $stock
        ];

        foreach ($bindParams as $value => $key) {
            $statement->bindValue($value, $key, PDO::PARAM_STR);
        }

        try {
            $statement->execute();

            return $statement->rowCount() > 0;
        } catch (PDOException $e) {
            throw new RuntimeException("Database Error: " . $e->getMessage());
        }
    }


    /**
     * Edit a product in the database.
     *
     * @param int $productID The ID of the product to edit.
     * @param array $payload The payload containing the product details.
     * @throws InvalidArgumentException If the product ID parameter is invalid or missing.
     * @throws RuntimeException If there is a database error.
     * @return bool Returns true if the product is successfully edited, false otherwise.
     */
    public function editProduct($productID, $payload)
    {

        if (!is_array($payload) || empty($payload) || !$productID) {
            throw new InvalidArgumentException("Invalid or missing product ID parameter");
        }

        $productPhotoURL = $payload['product_photo_url'];
        $productName = $payload['product_name'];
        $productCategory = $payload['product_category'];
        $productSubCategory = $payload['product_sub_category'] ?? '';
        $productPrice = $payload['product_price'];
        $size = $payload['pot_size'] ?? '';
        $productDescription = $payload['product_description'];

        $categoryID = $this->categoriesModel->getCategoryByName($productCategory);
        $categoryID = $categoryID[0]['id'];

        $subCategoryID = $this->subCategoriesModel->getSubCategoryByName($productSubCategory);
        $subCategoryID = $subCategoryID[0]['id'];

        $query = "UPDATE " . self::PRODUCTS_TABLE . " SET categoryId = :categoryID, subCategoryId = :subCategoryID, product_name = :productName, product_description = :productDescription, size = :size, product_image = :productImage, product_price = :productPrice WHERE id = :id";
        $statement = $this->pdo->prepare($query);

        $bindParams = [
            ':id' => $productID,
            ':categoryID' => $categoryID,
            ':subCategoryID' => $subCategoryID,
            ':productName' => $productName,
            ':productDescription' => $productDescription,
            ':size' => $size,
            ':plantImage' => $productPhotoURL,
            ':productPrice' => $productPrice,
        ];

        foreach ($bindParams as $key => $value) {
            $statement->bindValue($key, $value, PDO::PARAM_STR);
        }

        try {
            $statement->execute();

            return $statement->rowCount() > 0;
        } catch (PDOException $e) {
            throw new RuntimeException("Database Error: " . $e->getMessage());
        }
    }


    /**
     * Deletes a product from the database.
     *
     * @param int $productID The ID of the product to be deleted.
     * @throws InvalidArgumentException If the product ID is invalid or missing.
     * @throws RuntimeException If there is a database error.
     * @return bool Returns true if the product is successfully deleted, false otherwise.
     */
    public function deleteProduct($productID)
    {
        if (!$productID) {
            throw new InvalidArgumentException("Invalid or missing product ID parameter");
        }

        $delete_query = "DELETE FROM " . self::PRODUCTS_TABLE . " WHERE id = :id";

        $delete_query = $this->pdo->prepare($delete_query);
        $delete_query->bindValue(':id', $productID, PDO::PARAM_STR);

        try {
            $delete_query->execute();
            return $delete_query->rowCount() > 0;
        } catch (PDOException $e) {
            throw new RuntimeException("Database Error: " . $e->getMessage());
        }
    }
}
