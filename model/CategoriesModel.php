<?php

namespace Models;

use Helpers\ResponseHelper;

use Models\SubCategoriesModel;

use PDO;
use PDOException;

use RuntimeException;
use InvalidArgumentException;

class CategoriesModel
{
    private $pdo;
    private $subCategoriesModel;

    private const PRODUCTS_CATEGORIES_TABLE = 'products_categories_tb';

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->subCategoriesModel = new SubCategoriesModel($pdo);
        $this->column_names = ['id', 'name', 'description'];
    }

    public function getAllPlantCategories()
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
                    if (!isset($categories[$key]['sub_categories'])) {
                        $categories[$key]['sub_categories'] = [];
                    }

                    // Append the sub categories
                    foreach ($subCategory as $item) {
                        $categories[$key]['sub_categories'][] = $item;
                    }
                }
            }

            return $categories;
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    public function getCategoryById($id)
    {
        if (!$id) {
            throw new InvalidArgumentException('Invalid category ID');
        }

        $query = "SELECT * FROM " . self::PRODUCTS_CATEGORIES_TABLE . " WHERE id = :id";

        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_STR);

        try {
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    public function getCategoryByName($name)
    {
        if (!is_string($name) && empty($name)) {
            throw new InvalidArgumentException('Invalid category name');
        }

        $query = "SELECT * FROM " . self::PRODUCTS_CATEGORIES_TABLE . " WHERE name = :name";

        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':name', $name, PDO::PARAM_STR);

        try {
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    public function addNewCategory($payload)
    {
        if (!is_array($payload) && empty($payload)) {
            throw new InvalidArgumentException('Invalid category payload or empty payload');
        }

        $categoryName = $payload['category_name'];
        $categoryDescription = $payload['category_description'];

        $query = "INSERT INTO " . self::PRODUCTS_CATEGORIES_TABLE . " (name, description) VALUES (:categoryName, :categoryDescription)";

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

    public function editCategory($id, $data)
    {
        if (!is_array($data) || empty($data) || !$id) {
            throw new InvalidArgumentException('Invalid category data or empty data');
        }

        $categoryName = $data['category_name'];
        $categoryDescription = $data['category_description'];

        $query = "UPDATE " . self::PRODUCTS_CATEGORIES_TABLE . " SET name = :categoryName, description = :categoryDescription WHERE id = :id";

        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);
        $statement->bindValue(':categoryDescription', $categoryDescription, PDO::PARAM_STR);
        $statement->bindValue(':id', $id, PDO::PARAM_STR);

        try {
            $statement->execute();
            return $statement->rowCount() > 0;
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    public function deleteCategory($id)
    {
        if (!$id) {
            throw new InvalidArgumentException('Invalid category ID');
        }

        $query = "DELETE FROM products_tb WHERE categoryId = :id";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_STR);

        try {
            $statement->execute();

            $query = "DELETE FROM products_sub_categories_tb WHERE category_id = :id";

            $statement = $this->pdo->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_STR);

            $statement->execute();

            $query = "DELETE FROM " . self::PRODUCTS_CATEGORIES_TABLE . " WHERE id = :id";
            $statement = $this->pdo->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_STR);

            $statement->execute();
            return $statement->rowCount() > 0;
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    public function getCategoriesColumnBy($column, $condition_column, $condition_value)
    {
        if (!is_string($column) || !is_string($condition_column) || !$condition_value) {
            throw new InvalidArgumentException('Invalid column name or condition column name or condition value');
        }

        $condition = null;

        foreach ($this->column_names as $column_name) {
            if ($column_name === $column) {
                $condition = "$condition_column = :value";
                break;
            }
        }

        $query = "SELECT $column FROM " . self::PRODUCTS_CATEGORIES_TABLE . " WHERE $condition";

        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':value', $condition_value, PDO::PARAM_STR);

        try {
            $statement->execute();
            return $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }
}
