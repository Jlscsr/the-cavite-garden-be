<?php

namespace Models;

use Helpers\ResponseHelper;
use Models\SubCategoriesModel;

use PDO;

class CategoriesModel
{
    private $pdo;
    private $sub_categories_model;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->sub_categories_model = new SubCategoriesModel($pdo);
    }

    public function getAllPlantCategories()
    {
        $query = "SELECT * FROM products_categories_tb";
        $statement = $this->pdo->prepare($query);

        try {
            $statement->execute();
            $categories = $statement->fetchAll(PDO::FETCH_ASSOC);


            foreach ($categories as $key => $category) {
                $sub_category = $this->sub_categories_model->getSubCategoryByCategoryId($category['id']);

                if (!empty($sub_category)) {
                    if (!isset($categories[$key]['sub_categories'])) {
                        $categories[$key]['sub_categories'] = [];
                    }

                    // Append the sub categories
                    foreach ($sub_category as $sub_category_item) {
                        $categories[$key]['sub_categories'][] = $sub_category_item;
                    }
                }
            }

            return $categories;
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getCategoryById($id)
    {
        if (!is_integer($id)) {
            return [];
        }

        $query = "SELECT * FROM products_categories_tb WHERE id = :id";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_STR);

        try {
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getCategoryByName($name)
    {
        if (!is_string($name)) {
            return [];
        }

        $query = "SELECT * FROM products_categories_tb WHERE name = :name";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':name', $name, PDO::PARAM_STR);

        try {
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function addNewCategory($data)
    {
        if (!is_array($data) && empty($data)) {
            return [];
        }

        $category_name = $data['category_name'];
        $category_description = $data['category_description'];

        $query = "INSERT INTO products_categories_tb (name, description) VALUES (:category_name, :category_description)";
        $query = $this->pdo->prepare($query);
        $query->bindValue(':category_name', $category_name, PDO::PARAM_STR);
        $query->bindValue(':category_description', $category_description, PDO::PARAM_STR);

        try {
            $query->execute();
            return $query->rowCount() > 0;
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }

    public function editCategory($id, $data)
    {
        if (!is_array($data) || empty($data) || !is_string($id)) {
            return [];
        }

        $category_name = $data['category_name'];
        $category_description = $data['category_description'];

        $update_category_query = "UPDATE products_categories_tb SET name = :category_name, description = :category_description WHERE id = :id";
        $update_category_query = $this->pdo->prepare($update_category_query);
        $update_category_query->bindValue(':category_name', $category_name, PDO::PARAM_STR);
        $update_category_query->bindValue(':category_description', $category_description, PDO::PARAM_STR);
        $update_category_query->bindValue(':id', $id, PDO::PARAM_STR);

        try {
            $update_category_query->execute();
            return $update_category_query->rowCount() > 0;
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }

    public function deleteCategory($id)
    {
        if (!is_string($id)) {
            return [];
        }

        $delete_all_plants_query = "DELETE FROM products_tb WHERE categoryId = :id";
        $delete_all_plants_query = $this->pdo->prepare($delete_all_plants_query);
        $delete_all_plants_query->bindValue(':id', $id, PDO::PARAM_STR);

        try {
            $delete_all_plants_query->execute();

            $delete_query = "DELETE FROM products_categories_tb WHERE id = :id";
            $delete_query = $this->pdo->prepare($delete_query);
            $delete_query->bindValue(':id', $id, PDO::PARAM_STR);

            $delete_query->execute();
            return $delete_query->rowCount() > 0;
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }
}
