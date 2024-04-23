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
            return [];
        }
    }

    /**
     * Retrieves all plants from the database that belong to a specific category.
     *
     * @param string $category_id The ID of the category.
     * @return array An array of associative arrays representing the plants.
     */
    public function getAllPlantsByCategory($category_id)
    {
        if (!is_string($category_id)) {
            return [];
        }

        $query = "SELECT * FROM products_tb WHERE categoryId = :category_id";
        $statement = $this->pdo->prepare($query);

        $statement->bindValue(':category_id', $category_id, PDO::PARAM_STR);

        try {
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }


    /**
     * Retrieves the details of a plant by its ID.
     *
     * @param int $plant_id The ID of the plant.
     * @return array An associative array containing the details of the plant, including the category and sub-category names.
     */
    public function getPlantById($plant_id)
    {
        $plant_id = (int)$plant_id;
        if (!$plant_id) {
            return [];
        }

        $query = "SELECT * FROM products_tb WHERE id = :plant_id";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':plant_id', $plant_id, PDO::PARAM_STR);

        try {
            $statement->execute();

            if ($statement->rowCount() > 0) {
                $plant_details = $statement->fetch(PDO::FETCH_ASSOC);
                $category_id = $plant_details['categoryId'];
                $sub_category_id = $plant_details['subCategoryId'];

                $category_name = $this->categories_model->getCategoriesColumnBy('name', 'id', $category_id);
                $sub_category_name = $this->sub_categories_model->getSubCategoryNameById($sub_category_id);


                $plant_details['category_name'] = $category_name['name'] ?? null;
                $plant_details['sub_category_name'] = $sub_category_name['name'] ?? null;

                return $plant_details;
            }
        } catch (PDOException $e) {
            return [];
        }
    }


    /**
     * Adds a new plant to the products table based on the provided data.
     *
     * @param array $data An array containing the details of the new plant.
     * @throws PDOException If an error occurs during database operation.
     * @return bool Whether the plant was successfully added or not.
     */
    public function addNewPlant($data)
    {

        if (!is_array($data) && empty($data)) {
            return [];
        }

        $product_photo_url = $data['product_photo_url'];
        $plant_name = $data['product_name'];
        $plant_category = $data['product_category'];
        $plant_sub_category = $data['product_sub_category'] ?? '';
        $plant_price = $data['product_price'];
        $pot_size = $data['pot_size'] ?? '';
        $stock = $data['stock'];
        $product_description = $data['product_description'];

        $category_id = $this->categories_model->getCategoriesColumnBy('id', 'name', $plant_category);

        $category_id = null;

        if ($plant_sub_category !== '') {
            $get_sub_category_id_query = "SELECT id FROM products_sub_categories_tb WHERE name = :sub_category";
            $statement = $this->pdo->prepare($get_sub_category_id_query);
            $statement->bindValue(':sub_category', $plant_sub_category, PDO::PARAM_STR);

            try {
                $statement->execute();
                $sub_category_id = $statement->fetchColumn();
            } catch (PDOException $e) {
                ResponseHelper::sendErrorResponse($e->getMessage(), 500);
            }
        }

        if (!$sub_category_id) {
            $sub_category_id = null;
        }

        if ($category_id === null) {
            return [];
        }

        $add_new_plant_query = "INSERT INTO products_tb (categoryId, subCategoryId, plant_name, plant_description, size, plant_image, plant_price, stock) VALUES (:category_id, :sub_category_id, :plant_name, :plant_description, :size, :plant_image, :plant_price, :stock)";
        $add_new_plant_query = $this->pdo->prepare($add_new_plant_query);
        $add_new_plant_query->bindValue(':category_id', $category_id, PDO::PARAM_STR);
        $add_new_plant_query->bindValue(':sub_category_id', $sub_category_id, PDO::PARAM_STR);
        $add_new_plant_query->bindValue(':plant_name', $plant_name, PDO::PARAM_STR);
        $add_new_plant_query->bindValue(':plant_description', $product_description, PDO::PARAM_STR);
        $add_new_plant_query->bindValue(':size', $pot_size, PDO::PARAM_STR);
        $add_new_plant_query->bindValue(':plant_image', $product_photo_url, PDO::PARAM_STR);
        $add_new_plant_query->bindValue(':plant_price', $plant_price, PDO::PARAM_STR);
        $add_new_plant_query->bindValue(':stock', $stock, PDO::PARAM_STR);

        try {
            $add_new_plant_query->execute();

            return $add_new_plant_query->rowCount() > 0;
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
    public function editPlant($id, $data)
    {

        if (!is_array($data) || empty($data) || !is_string($id)) {
            return [];
        }

        $plant_photo_url = $data['product_photo_url'];
        $plant_name = $data['product_name'];
        $plant_category = $data['product_category'];
        $plant_sub_category = $data['product_sub_category'] ?? '';
        $plant_price = $data['product_price'];
        $pot_size = $data['pot_size'] ?? '';
        $product_description = $data['product_description'];

        $category_id = $this->categories_model->getCategoryByName($plant_category);
        $category_id = $category_id[0]['id'];

        $sub_category_id = $this->sub_categories_model->getSubCategoryByName($plant_sub_category);
        $sub_category_id = $sub_category_id[0]['id'];

        $update_plant_query = "UPDATE products_tb SET categoryId = :category_id, subCategoryId = :sub_category_id, plant_name = :plant_name, plant_description = :plant_description, size = :size, plant_image = :plant_image, plant_price = :plant_price WHERE id = :id";
        $update_plant_query = $this->pdo->prepare($update_plant_query);
        $update_plant_query->bindValue(':category_id', $category_id, PDO::PARAM_STR);
        $update_plant_query->bindValue(':sub_category_id', $sub_category_id, PDO::PARAM_STR);
        $update_plant_query->bindValue(':plant_name', $plant_name, PDO::PARAM_STR);
        $update_plant_query->bindValue(':plant_description', $product_description, PDO::PARAM_STR);
        $update_plant_query->bindValue(':size', $pot_size, PDO::PARAM_STR);
        $update_plant_query->bindValue(':plant_image', $plant_photo_url, PDO::PARAM_STR);
        $update_plant_query->bindValue(':plant_price', $plant_price, PDO::PARAM_STR);
        $update_plant_query->bindValue(':id', $id, PDO::PARAM_STR);

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
    public function deletePlant($id)
    {
        if (!is_string($id)) {
            return [];
        }

        $delete_query = "DELETE FROM products_tb WHERE id = :id";
        $delete_query = $this->pdo->prepare($delete_query);
        $delete_query->bindValue(':id', $id, PDO::PARAM_STR);

        try {
            $delete_query->execute();
            return $delete_query->rowCount() > 0;
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }
}
