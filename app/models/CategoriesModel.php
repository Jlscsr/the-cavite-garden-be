<?php

namespace App\Models;

use PDO;
use PDOException;

use RuntimeException;
use InvalidArgumentException;

use App\Models\SubCategoriesModel;

class CategoriesModel
{
    private $pdo;
    private $subCategoriesModel;

    private const PRODUCTS_CATEGORIES_TABLE = 'products_categories_tb';
    private const PRODUCTS_SUB_CATEGORIES_TABLE = 'products_sub_categories_tb';

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->subCategoriesModel = new SubCategoriesModel($pdo);
    }

    /**
     * Retrieves all categories from the database.
     *
     * This function fetches all product categories from the database table, 
     *                          along with their subcategories if they exist.
     *
     * @throws RuntimeException If there is an error during the retrieval process.
     * @return array An array containing all the product categories with their subcategories.
     */
    public function getAllCategories(): array
    {
        $query = "SELECT * FROM " . self::PRODUCTS_CATEGORIES_TABLE;

        $statement = $this->pdo->prepare($query);

        try {
            $statement->execute();
            $categories = $statement->fetchAll(PDO::FETCH_ASSOC);


            foreach ($categories as $key => $category) {
                $categoryID = (int) $category['id'];
                $subCategory = $this->subCategoriesModel->getSubCategoryByCategoryId($categoryID);

                if (!empty($subCategory)) {
                    if (!isset($categories[$key]['subCategories'])) {
                        $categories[$key]['subCategories'] = [];
                    }

                    foreach ($subCategory as $item) {
                        $categories[$key]['subCategories'][] = $item;
                    }
                }
            }

            return $categories;
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    /**
     * Retrieves a category by its ID from the database.
     *
     * @param int $id The ID of the category to retrieve.
     * @throws InvalidArgumentException If the category ID is invalid.
     * @throws RuntimeException If there is an error during the retrieval process.
     * @return array An array containing the category information.
     */
    public function getCategoryById(int $id): array
    {
        $query = "SELECT * FROM " . self::PRODUCTS_CATEGORIES_TABLE . " WHERE id = :id";

        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        try {
            $statement->execute();
            return $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    /**
     * Retrieves a category by its name from the database.
     *
     * @param string $categoryName The name of the category to retrieve.
     * @throws RuntimeException If there is an error during the retrieval process.
     * @return array An array containing the category information.
     */
    public function getCategoryByName(string $categoryName): array
    {
        $query = "SELECT * FROM " . self::PRODUCTS_CATEGORIES_TABLE . " WHERE categoryName = :categoryName";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);

        try {
            $statement->execute();
            return $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    /**
     * Retrieves the ID column of a category by its name from the database.
     *
     * @param string $categoryName The name of the category to retrieve.
     * @throws RuntimeException If there is an error during the retrieval process.
     * @return array An array containing the ID of the category.
     */
    public function getCategoryIDColumn(string $categoryName): array
    {
        $query = "SELECT id FROM " . self::PRODUCTS_CATEGORIES_TABLE . " WHERE categoryName = :categoryName";
        $statement = $this->pdo->prepare($query);

        $statement->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);

        try {
            $statement->execute();

            return $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    /**
     * Adds a new category to the database.
     *
     * @param array $payload The data for the new category.
     * @throws RuntimeException If an error occurs during the process.
     * @return bool True if the category was added successfully, false otherwise.
     */
    public function addNewCategory(array $payload): bool
    {
        $categoryName = $payload['categoryName'];
        $categoryDescription = $payload['categoryDescription'];

        $query = "INSERT INTO " . self::PRODUCTS_CATEGORIES_TABLE . " (categoryName, categoryDescription) VALUES (:categoryName, :categoryDescription)";
        $statement = $this->pdo->prepare($query);

        $statement->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);
        $statement->bindValue(':categoryDescription', $categoryDescription, PDO::PARAM_STR);

        try {
            $statement->execute();

            return $statement->rowCount() > 0;
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    /**
     * Edits a category in the database.
     *
     * @param int $categoryID The ID of the category to edit.
     * @param array $payload The data to update the category with.
     *                      Should contain 'categoryName' and 'categoryDescription' keys.
     * @throws RuntimeException If there is an error during the update process.
     * @return bool True if the category was successfully updated, false otherwise.
     */
    public function editCategory(int $categoryID, array $payload): bool
    {
        $categoryName = $payload['categoryName'];
        $categoryDescription = $payload['categoryDescription'];

        $query = "UPDATE " . self::PRODUCTS_CATEGORIES_TABLE . " SET categoryName = :categoryName, categoryDescription = :categoryDescription WHERE id = :id";
        $statement = $this->pdo->prepare($query);

        $statement->bindValue(':id', $categoryID, PDO::PARAM_INT);
        $statement->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);
        $statement->bindValue(':categoryDescription', $categoryDescription, PDO::PARAM_STR);

        try {
            $statement->execute();

            return $statement->rowCount() > 0;
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    /**
     * Deletes a category based on the provided categoryID.
     *
     * @param int $categoryID The ID of the category to delete.
     * @throws RuntimeException If there is an error during the deletion process.
     * @return bool True if the category was successfully deleted, false otherwise.
     */
    public function deleteCategory(int $categoryID): bool
    {
        $query = "DELETE FROM " . self::PRODUCTS_SUB_CATEGORIES_TABLE . " WHERE categoryID = :id";
        $statement = $this->pdo->prepare($query);

        $statement->bindValue(':id', $categoryID, PDO::PARAM_INT);

        try {
            $statement->execute();

            $query = "DELETE FROM " . self::PRODUCTS_CATEGORIES_TABLE . " WHERE id = :id";
            $statement = $this->pdo->prepare($query);

            $statement->bindValue(':id', $categoryID, PDO::PARAM_INT);

            $statement->execute();

            return $statement->rowCount() > 0;
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }
}
