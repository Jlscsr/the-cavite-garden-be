<?php

namespace Models;

use Helpers\ResponseHelper;
use PDO;

class SubCategoriesModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;

        $this->column_names = ['id', 'name', 'description'];
    }

    public function getAllSubCategories()
    {
        $query = "SELECT * FROM products_sub_categories_tb";
        $statement = $this->pdo->prepare($query);

        try {
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
            exit;
        }
    }

    public function getSubCategoryById($sub_category_id)
    {
        $query = "SELECT * FROM products_sub_categories_tb WHERE id = :sub_category_id";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':sub_category_id', $sub_category_id, PDO::PARAM_STR);

        try {
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
            exit;
        }
    }

    public function getSubCategoryByName($sub_category_name)
    {
        $query = "SELECT * FROM products_sub_categories_tb WHERE name = :sub_category_name";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':sub_category_name', $sub_category_name, PDO::PARAM_STR);

        try {
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
            exit;
        }
    }

    public function getSubCategoryByCategoryId($category_id)
    {
        $query = "SELECT * FROM products_sub_categories_tb WHERE category_id = :category_id";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':category_id', $category_id, PDO::PARAM_STR);

        try {
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
            exit;
        }
    }

    public function addNewSubCategory($payload)
    {
        if (!is_array($payload) && empty($payload)) {
            return [];
        }

        $category_id = $payload['category_id'];
        $sub_category_name = $payload['sub_category_name'];
        $category_description = $payload['category_description'];

        $query = "INSERT INTO products_sub_categories_tb (category_id,name, description) VALUES (:category_id, :sub_category_name, :category_description)";
        $query = $this->pdo->prepare($query);
        $query->bindValue(':category_id', $category_id, PDO::PARAM_STR);
        $query->bindValue(':sub_category_name', $sub_category_name, PDO::PARAM_STR);
        $query->bindValue(':category_description', $category_description, PDO::PARAM_STR);

        try {
            $query->execute();
            return $query->rowCount() > 0;
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }

    public function getCategoriesColumnBy($column, $condition_column, $condition_value)
    {
        if (!is_string($column) || !is_string($condition_column) || !is_string($condition_value)) {
            return [];
        }

        $condition = null;

        foreach ($this->column_names as $column_name) {
            if ($column_name === $column) {
                $condition = "$condition_column = :value";
                break;
            }
        }

        $query = "SELECT $column FROM products_categories_tb WHERE $condition";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':value', $condition_value, PDO::PARAM_STR);

        try {
            $statement->execute();
            return $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }

    public function getSubCategoriesColumnBy($column, $condition_column, $condition_value)
    {
        if (!is_string($column) || !is_string($condition_column) || !is_string($condition_value)) {
            return [];
        }

        $condition = null;

        foreach ($this->column_names as $column_name) {
            if ($column_name === $column) {
                $condition = "$condition_column = :value";
                break;
            }
        }

        $query = "SELECT $column FROM products_sub_categories_tb WHERE $condition";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':value', $condition_value, PDO::PARAM_STR);

        try {
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }
}
