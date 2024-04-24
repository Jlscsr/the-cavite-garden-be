<?php

use Models\PlantModel;

use Helpers\ResponseHelper;
use Helpers\HeaderHelper;


use Models\CategoriesModel;
use Models\SubCategoriesModel;

class PlantController
{
    private $plant_model;
    private $categories_model;
    private $sub_categories_model;

    public function __construct($pdo)
    {
        $this->plant_model = new PlantModel($pdo);
        $this->categories_model = new CategoriesModel($pdo);
        $this->sub_categories_model = new SubCategoriesModel($pdo);

        HeaderHelper::setResponseHeaders();
    }

    /**
     * Retrieves all plants from the database and returns them with their associated category and subcategory names.
     *
     * @return void
     */
    public function getAllPlants()
    {

        $plants = $this->plant_model->getAllPlants();

        if (empty($plants)) {
            ResponseHelper::sendErrorResponse("No plants found", 404);
            return;
        }

        $plants = $this->addCategoryAndSubCategoryNamesToPlants($plants);

        ResponseHelper::sendSuccessResponse($plants, 'Plants retrieved successfully');
        return;
    }

    /**
     * Retrieves a plant by its ID and returns it with its associated category and subcategory names.
     *
     * @param array $params An array containing the ID of the plant to retrieve.
     * @throws ResponseHelperException If the parameter type is invalid or if no plant is found by the given ID.
     * @return void
     */
    public function getProductByID($params)
    {

        $plant_id = $params['id'];

        if (!is_string($plant_id)) {
            ResponseHelper::sendErrorResponse("Invalid parameter type.", 400);
            return;
        }

        $plants = $this->plant_model->getProductByID($plant_id);
        $plant_category = $this->categories_model->getCategoryById($plants['categoryId']);
        $plant_sub_category = $this->sub_categories_model->getSubCategoryById($plants['subCategoryId']);
        $plants['category_name'] = $plant_category[0]['name'];
        $plants['sub_category_name'] = $plant_sub_category[0]['name'];

        if (empty($plants) || empty($plant_category)) {
            ResponseHelper::sendErrorResponse("No plants found by that id.", 404);
            return;
        }

        ResponseHelper::sendSuccessResponse($plants, 'Plants retrieved successfully');
        return;
    }

    /**
     * Retrieves all plants by category from the database and adds category and subcategory names to each plant.
     *
     * @param array $params An array containing the category ID to retrieve plants.
     * @throws ResponseHelperException If the parameter type is invalid or if no plants are found for the given category.
     * @return void
     */
    public function getAllProductsByCategory($params)
    {
        // print the variable type of type_id
        $category_id = $params['id'];

        if (!is_string($category_id)) {
            ResponseHelper::sendErrorResponse("Invalid plant type.", 400);
            return;
        }

        $plants = $this->plant_model->getAllProductsByCategory($category_id);

        $plants = $this->addCategoryAndSubCategoryNamesToPlants($plants);

        if (empty($plants)) {
            ResponseHelper::sendSuccessResponse([], "No plants found base on the Category", 200);
            return;
        }

        ResponseHelper::sendSuccessResponse($plants, 'Plant retrieved successfully');
        return;
    }

    /**
     * Adds a new plant to the database.
     *
     * @param mixed $data The data of the plant to be added.
     * @throws ResponseHelperException If the data is invalid or empty.
     * @throws ResponseHelperException If the plant could not be added.
     * @return void
     */
    public function addNewProduct($data)
    {
        if (!is_array($data) && empty($data)) {
            ResponseHelper::sendErrorResponse("Invalid data or data is empty", 400);
            return;
        }


        $response = $this->plant_model->addNewProduct($data);

        if (empty($response)) {
            ResponseHelper::sendErrorResponse("Failed to add new plant", 500);
            return;
        }

        ResponseHelper::sendSuccessResponse(null, 'Plant added successfully', 201);
        return;
    }

    /**
     * Edit a plant.
     *
     * @param array $params An array containing the ID of the plant to edit.
     * @param mixed $data The data of the plant to be edited.
     * @throws ResponseHelperException If the data is invalid or empty.
     * @throws ResponseHelperException If the plant could not be edited.
     * @return void
     */
    public function editPlant($params, $data)
    {

        if (!is_array($data) || empty($data) || !is_array($params) || empty($params['id'])) {
            ResponseHelper::sendErrorResponse("Invalid data or data is empty", 400);
            return;
        }


        $response = $this->plant_model->editPlant($params['id'], $data);
        if (empty($response)) {
            ResponseHelper::sendErrorResponse("Failed to edit plant", 500);
            return;
        }

        ResponseHelper::sendSuccessResponse(null, 'Plant edited successfully', 201);
        return;
    }

    /**
     * Deletes a plant from the database.
     *
     * @param array $param An array containing the ID of the plant to delete.
     * @throws ResponseHelperException If the parameter is invalid or empty.
     * @throws ResponseHelperException If the plant could not be deleted.
     * @return void
     */
    public function deletePlant($param)
    {
        if (!is_array($param) || !isset($param['id']) || empty($param)) {
            ResponseHelper::sendErrorResponse("Invalid data or data is empty", 400);
            return;
        }


        $response = $this->plant_model->deletePlant($param['id']);

        if (!$response) {
            ResponseHelper::sendErrorResponse("Failed to delete plant", 500);
            return;
        }

        ResponseHelper::sendSuccessResponse(null, 'Plant deleted successfully');
        return;
    }

    /**
     * Retrieves and adds category and subcategory names to the given plants array.
     *
     * @param array $plants The array of plants to add names to.
     * @return array The updated plants array with category and subcategory names.
     */
    private function addCategoryAndSubCategoryNamesToPlants($plants)
    {
        $plants_copy = $plants;

        foreach ($plants_copy as $key => &$value) {
            $category = $this->categories_model->getCategoryById($value['categoryId']);
            $value['category_name'] = $category[0]['name'];
        }
        unset($value);

        foreach ($plants_copy as $key => &$value) {
            $sub_category = $this->sub_categories_model->getSubCategoryById($value['subCategoryId']);

            if (!empty($sub_category)) {
                $value['sub_category_name'] = $sub_category[0]['name'];
            }
        }
        unset($value);

        return $plants_copy;
    }
}
