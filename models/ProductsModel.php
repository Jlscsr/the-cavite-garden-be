<?php

namespace Models;

use Models\CategoriesModel;
use Models\SubCategoriesModel;

use PDO;

use PDOException;
use RuntimeException;

class ProductsModel
{
    private $pdo;
    private $categoriesModel;
    private $subCategoriesModel;

    private const PRODUCTS_TABLE = 'products_tb';
    private const CATEGORIES_TABLE = 'products_categories_tb';
    private const SUB_CATEGORIES_TABLE = 'products_sub_categories_tb';

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->categoriesModel = new CategoriesModel($pdo);
        $this->subCategoriesModel = new SubCategoriesModel($pdo);
    }

    /**
     * Retrieves all products from the database, along with their category and subcategory names.
     *
     * @return array An array of associative arrays representing the products with their category and subcategory names.
     * @throws RuntimeException Database Error: if there is an issue with the database connection.
     */
    public function getAllProducts()
    {
        try {
            $query = "
                SELECT p.*, c.categoryName as categoryName,
                CASE 
                    WHEN p.subCategoryId IS NULL THEN NULL
                    ELSE sc.subCategoryName
                END as subCategoryName
                FROM " . self::PRODUCTS_TABLE . " p
                JOIN " . self::CATEGORIES_TABLE . " c ON p.categoryID = c.id
                LEFT JOIN " . self::SUB_CATEGORIES_TABLE . " sc ON p.subCategoryID = sc.id
            ";

            $statement = $this->pdo->query($query);

            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new RuntimeException("Database Error: " . $e->getMessage());
        }
    }

    /**
     * Retrieves a product from the database by its ID, along with its category and subcategory names.
     *
     * @param int $productID The ID of the product to retrieve.
     * @throws RuntimeException Database Error: if there is an issue with the database connection.
     * @return array|null An associative array representing the product with its category and subcategory names, or null if no product is found.
     */
    public function getProductByID($productID)
    {
        $query = "
                SELECT p.*, c.categoryName as categoryName,
                CASE 
                    WHEN p.subCategoryID IS NULL THEN NULL
                    ELSE sc.subCategoryName
                END as subCategoryName
                FROM " . self::PRODUCTS_TABLE . " p
                JOIN " . self::CATEGORIES_TABLE . " c ON p.categoryID = c.id
                LEFT JOIN " . self::SUB_CATEGORIES_TABLE . " sc ON p.subCategoryID = sc.id
                WHERE p.id = :plantID
            ";

        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':plantID', $productID, PDO::PARAM_INT);

        try {
            $statement->execute();

            return $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new RuntimeException("Database Error: " . $e->getMessage());
        }
    }

    /**
     * Retrieves all products from the database that belong to a specific category.
     *
     * @param int $categoryID The ID of the category.
     * @throws RuntimeException Database Error: if there is an issue with the database connection.
     * @return array An array of associative arrays representing the products with their category and subcategory names.
     */
    public function getAllProductsByCategory($categoryID)
    {
        $query = "
                SELECT p.*, c.categoryName as categoryName,
                CASE 
                    WHEN p.subCategoryID IS NULL THEN NULL
                    ELSE sc.subCategoryName
                END as subCategoryName
                FROM " . self::PRODUCTS_TABLE . " p
                JOIN " . self::CATEGORIES_TABLE . " c ON p.categoryID = c.id
                LEFT JOIN " . self::SUB_CATEGORIES_TABLE . " sc ON p.subCategoryID = sc.id
                WHERE c.id = :categoryID
            ";

        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':categoryID', $categoryID, PDO::PARAM_INT);

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
     *                       It should have the following keys:
     *                       - productPhotoURL: The URL of the product photo.
     *                       - productName: The name of the product.
     *                       - productCategory: The category of the product.
     *                       - productSubCategory: The sub-category of the product (optional).
     *                       - productPrice: The price of the product.
     *                       - productSize: The size of the product.
     *                       - productStock: The stock of the product.
     *                       - productDescription: The description of the product.
     * @throws RuntimeException If there is a database error.
     * @return bool Returns true if the product is successfully added, false otherwise.
     */
    public function addNewProduct($payload)
    {
        $productPhotoURL = $payload['productPhotoURL'];
        $productName = $payload['productName'];
        $productCategory = $payload['productCategory'];
        $productSubCategory = $payload['productSubCategory'];
        $productPrice = $payload['productPrice'];
        $productSize = $payload['productSize'];
        $productStock = $payload['productStock'];
        $productDescription = $payload['productDescription'];

        $categoryID = $this->categoriesModel->getCategoryIDColumn($productCategory);

        $subCategoryID = $productSubCategory ? $this->subCategoriesModel->getSubCategoryIDColumn($productSubCategory) : null;

        $categoryID = $categoryID['id'];
        $subCategoryID = isset($subCategoryID['id']) ? $subCategoryID['id'] : null;

        $query = "INSERT INTO " . self::PRODUCTS_TABLE . " (categoryID, subCategoryID, productName, productDescription, productImage, productStock, productSize, productPrice) VALUES (:categoryID, :subCategoryID, :productName, :productDescription, :productImage, :productStock, :productSize, :productPrice)";
        $statement = $this->pdo->prepare($query);

        $bindParams = [
            ':categoryID' => $categoryID,
            ':subCategoryID' => $subCategoryID,
            ':productName' => $productName,
            ':productDescription' => $productDescription,
            ':productImage' => $productPhotoURL,
            ':productStock' => $productStock,
            ':productSize' => $productSize,
            ':productPrice' => $productPrice,
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
     * Edits a product in the database.
     *
     * @param int $productID The ID of the product to edit.
     * @param array $payload An associative array containing the updated product data.
     *                       The array should have the following keys:
     *                       - productPhotoURL (string): The URL of the product's photo.
     *                       - productName (string): The name of the product.
     *                       - productCategory (string): The category of the product.
     *                       - productSubCategory (string|null): The subcategory of the product (optional).
     *                       - productPrice (float): The price of the product.
     *                       - productSize (string): The size of the product.
     *                       - productStock (int): The stock quantity of the product.
     *                       - productDescription (string): The description of the product.
     * @throws RuntimeException If there is an error during the database operation.
     * @return bool True if the product was successfully edited, false otherwise.
     */
    public function editProduct($productID, $payload)
    {
        $productPhotoURL = $payload['productPhotoURL'];
        $productName = $payload['productName'];
        $productCategory = $payload['productCategory'];
        $productSubCategory = $payload['productSubCategory'];
        $productPrice = $payload['productPrice'];
        $productSize = $payload['productSize'];
        $productStock = $payload['productStock'];
        $productDescription = $payload['productDescription'];

        $categoryID = $this->categoriesModel->getCategoryIDColumn($productCategory);
        $subCategoryID = $productSubCategory ? $this->subCategoriesModel->getSubCategoryIDColumn($productSubCategory) : null;

        $categoryID = $categoryID['id'];
        $subCategoryID = isset($subCategoryID['id']) ? $subCategoryID['id'] : null;

        $query = "UPDATE " . self::PRODUCTS_TABLE . " SET categoryID = :categoryID, subCategoryID = :subCategoryID, productName = :productName, productDescription = :productDescription, productImage = :productImage, productStock = :productStock, productSize = :productSize, productPrice = :productPrice WHERE id = :id";
        $statement = $this->pdo->prepare($query);

        $bindParams = [
            'id' => $productID,
            ':categoryID' => $categoryID,
            ':subCategoryID' => $subCategoryID,
            ':productName' => $productName,
            ':productDescription' => $productDescription,
            ':productImage' => $productPhotoURL,
            ':productStock' => $productStock,
            ':productSize' => $productSize,
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
     * Deletes a product from the database based on the provided ID.
     *
     * @param int $productID The ID of the product to be deleted.
     * @throws RuntimeException If there is an error during the database operation.
     * @return bool Returns true if the product is successfully deleted, false otherwise.
     */
    public function deleteProduct($productID)
    {
        $query = "DELETE FROM " . self::PRODUCTS_TABLE . " WHERE id = :id";
        $statement = $this->pdo->prepare($query);

        $statement->bindValue(':id', $productID, PDO::PARAM_INT);

        try {
            $statement->execute();
            return $statement->rowCount() > 0;
        } catch (PDOException $e) {
            throw new RuntimeException("Database Error: " . $e->getMessage());
        }
    }
}
