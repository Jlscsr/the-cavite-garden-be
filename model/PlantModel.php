<?php
require_once dirname(__DIR__) . '/model/CategoriesModel.php';
require_once dirname(__DIR__) . '/helpers/ResponseHelper.php';

class PlantModel
{
    private $pdo;
    private $categories_model;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->categories_model = new CategoriesModel($pdo);
    }

    public function getAllPlants()
    {
        $query = "SELECT * FROM plants_tb";
        $statement = $this->pdo->prepare($query);

        try {
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getPlantById($plant_id)
    {
        $plant_id = (int)$plant_id;
        if (!$plant_id) {
            return [];
        }

        $query = "SELECT * FROM plants_tb WHERE id = :plant_id";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':plant_id', $plant_id, PDO::PARAM_STR);

        try {
            $statement->execute();

            if ($statement->rowCount() > 0) {
                $plant_details = $statement->fetch(PDO::FETCH_ASSOC);
                $category_id = $plant_details['categoryId'];

                $query = "SELECT name FROM plant_categories WHERE id = :category_id";
                $statement = $this->pdo->prepare($query);
                $statement->bindValue(':category_id', $category_id, PDO::PARAM_STR);

                try {
                    $statement->execute();
                    $category_name = $statement->fetch(PDO::FETCH_ASSOC);
                    $plant_details['category_name'] = $category_name['name'];
                    return $plant_details;
                } catch (PDOException $e) {
                    return [];
                }
            }
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getAllPlantsByCategory($category_id)
    {
        if (!is_string($category_id)) {
            return [];
        }

        $query = "SELECT * FROM plants_tb WHERE categoryId = :category_id";
        $statement = $this->pdo->prepare($query);

        $statement->bindValue(':category_id', $category_id, PDO::PARAM_STR);

        try {
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getAllPlantCategories()
    {
        $query = "SELECT * FROM plant_categories";
        $statement = $this->pdo->prepare($query);

        try {
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function addNewPlant($data)
    {

        if (!is_array($data) && empty($data)) {
            return [];
        }

        $product_photo_url = $data['product_photo_url'];
        $plant_name = $data['product_name'];
        $plant_category = $data['product_category'];
        $plant_price = $data['product_price'];
        $pot_size = $data['pot_size'] ?? '';
        $stock = $data['stock'];
        $product_description = $data['product_description'];

        $get_category_id_query = "SELECT id FROM plant_categories WHERE name = :category";
        $statement = $this->pdo->prepare($get_category_id_query);
        $statement->bindValue(':category', $plant_category, PDO::PARAM_STR);

        $category_id = null;
        try {
            $statement->execute();
            $category_id = $statement->fetchColumn();
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }

        if ($category_id === null) {
            return [];
        }

        $add_new_plant_query = "INSERT INTO plants_tb (categoryId, plant_name, plant_description, size, plant_image, plant_price, stock) VALUES (:category_id, :plant_name, :plant_description, :size, :plant_image, :plant_price, :stock)";
        $add_new_plant_query = $this->pdo->prepare($add_new_plant_query);
        $add_new_plant_query->bindValue(':category_id', $category_id, PDO::PARAM_STR);
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

    public function editPlant($id, $data)
    {

        if (!is_array($data) || empty($data) || !is_string($id)) {
            return [];
        }

        $plant_photo_url = $data['product_photo_url'];
        $plant_name = $data['product_name'];
        $plant_category = $data['product_category'];
        $plant_price = $data['product_price'];
        $pot_size = $data['pot_size'] ?? '';
        $product_description = $data['product_description'];

        $category_id = $this->categories_model->getCategoryByName($plant_category);
        $category_id = $category_id[0]['id'];

        $update_plant_query = "UPDATE plants_tb SET categoryId = :category_id, plant_name = :plant_name, plant_description = :plant_description, size = :size, plant_image = :plant_image, plant_price = :plant_price WHERE id = :id";
        $update_plant_query = $this->pdo->prepare($update_plant_query);
        $update_plant_query->bindValue(':category_id', $category_id, PDO::PARAM_STR);
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

    public function deletePlant($id)
    {
        if (!is_string($id)) {
            return [];
        }

        $delete_query = "DELETE FROM plants_tb WHERE id = :id";
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
