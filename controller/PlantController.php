<?php

use Helpers\ResponseHelper;
use Helpers\HeaderHelper;

require_once dirname(__DIR__) . '/model/PlantModel.php';

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
    }

    public function getAllPlants()
    {
        HeaderHelper::setHeaders();

        $plants = $this->plant_model->getAllPlants();

        if (empty($plants)) {
            ResponseHelper::sendErrorResponse("No plants found", 404);
            return;
        }

        foreach ($plants as $key => &$value) {
            $category = $this->categories_model->getCategoryById($value['categoryId']);
            $value['category_name'] = $category[0]['name'];
        }
        unset($value);

        foreach ($plants as $key => &$value) {
            $sub_category = $this->sub_categories_model->getSubCategoryById($value['subCategoryId']);

            if (!empty($sub_category)) {
                $value['sub_category_name'] = $sub_category[0]['name'];
            }
        }
        unset($value);

        ResponseHelper::sendSuccessResponse($plants, 'Plants retrieved successfully');
        return;
    }

    public function getPlantById($params)
    {
        HeaderHelper::setHeaders();

        $plant_id = $params['id'];

        if (!is_string($plant_id)) {
            ResponseHelper::sendErrorResponse("Invalid parameter type.", 400);
            return;
        }

        $plants = $this->plant_model->getPlantById($plant_id);
        $plant_category = $this->categories_model->getCategoryById($plants[0]['categoryId']);
        $plants[0]['category_name'] = $plant_category[0]['name'];

        if (empty($plants) || empty($plant_category)) {
            ResponseHelper::sendErrorResponse("No plants found by that id.", 404);
            return;
        }

        ResponseHelper::sendSuccessResponse($plants, 'Plants retrieved successfully');
        return;
    }

    /**
     * Get all plants of a specific type.
     *
     * @param string $type The type of plant to retrieve
     */
    public function getAllPlantsByCategory($params)
    {
        HeaderHelper::setHeaders();
        // print the variable type of type_id
        $category_id = $params['id'];

        if (!is_string($category_id)) {
            ResponseHelper::sendErrorResponse("Invalid plant type.", 400);
            return;
        }

        $plants = $this->plant_model->getAllPlantsByCategory($category_id);

        foreach ($plants as $key => &$value) {
            $category = $this->categories_model->getCategoryById($value['categoryId']);
            $value['category_name'] = $category[0]['name'];
        }
        unset($value);

        foreach ($plants as $key => &$value) {
            $sub_category = $this->sub_categories_model->getSubCategoryById($value['subCategoryId']);

            if (!empty($sub_category)) {
                $value['sub_category_name'] = $sub_category[0]['name'];
            }
        }
        unset($value);

        if (empty($plants)) {
            ResponseHelper::sendSuccessResponse([], "No plants found base on the Category", 200);
            return;
        }

        ResponseHelper::sendSuccessResponse($plants, 'Plant retrieved successfully');
        return;
    }

    public function addNewPlant($data)
    {
        if (!is_array($data) && empty($data)) {
            ResponseHelper::sendErrorResponse("Invalid data or data is empty", 400);
            return;
        }

        HeaderHelper::setHeaders();

        $response = $this->plant_model->addNewPlant($data);

        if (empty($response)) {
            ResponseHelper::sendErrorResponse("Failed to add new plant", 500);
            return;
        }

        ResponseHelper::sendSuccessResponse(null, 'Plant added successfully', 201);
        return;
    }

    public function editPlant($params, $data)
    {

        if (!is_array($data) || empty($data) || !is_array($params) || empty($params['id'])) {
            ResponseHelper::sendErrorResponse("Invalid data or data is empty", 400);
            return;
        }

        HeaderHelper::setHeaders();

        $response = $this->plant_model->editPlant($params['id'], $data);
        if (empty($response)) {
            ResponseHelper::sendErrorResponse("Failed to edit plant", 500);
            return;
        }

        ResponseHelper::sendSuccessResponse(null, 'Plant edited successfully', 201);
        return;
    }

    public function deletePlant($param)
    {
        if (!is_array($param) || !isset($param['id']) || empty($param)) {
            ResponseHelper::sendErrorResponse("Invalid data or data is empty", 400);
            return;
        }

        HeaderHelper::setHeaders();

        $response = $this->plant_model->deletePlant($param['id']);

        if (!$response) {
            ResponseHelper::sendErrorResponse("Failed to delete plant", 500);
            return;
        }

        ResponseHelper::sendSuccessResponse(null, 'Plant deleted successfully');
        return;
    }
}
