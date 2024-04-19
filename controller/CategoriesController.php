<?php
require_once dirname(__DIR__) . '/model/CategoriesModel.php';
require_once dirname(__DIR__) . '/helpers/ResponseHelper.php';

class CategoriesController
{
    private $categories_model;

    public function __construct($pdo)
    {
        $this->categories_model = new CategoriesModel($pdo);
    }

    /**
     * Get all plant types and return them as JSON.
     */
    public function getAllPlantCategories()
    {
        HeaderHelper::setHeaders();

        $plant_types = $this->categories_model->getAllPlantCategories();

        if (empty($plant_types)) {
            ResponseHelper::sendErrorResponse("No plant types found", 404);
            return;
        }

        ResponseHelper::sendSuccessResponse($plant_types, 'Plant categories retrieved successfully');
        return;
    }

    public function addNewCategory($data)
    {
        if (!is_array($data) || empty($data)) {
            ResponseHelper::sendErrorResponse("Invalid parameter type.", 400);
            return;
        }

        HeaderHelper::setHeaders();

        $response = $this->categories_model->addNewCategory($data);

        if (empty($response)) {
            ResponseHelper::sendErrorResponse("Failed to add new plant", 500);
            return;
        }

        ResponseHelper::sendSuccessResponse(null, 'Category added successfully', 201);
        return;
    }

    public function editCategory($param, $data)
    {

        if (!is_array($data) || empty($data) || !is_array($param) || empty($param['id'])) {
            ResponseHelper::sendErrorResponse("Invalid data or data is empty", 400);
            return;
        }

        HeaderHelper::setHeaders();

        $response = $this->categories_model->editCategory($param['id'], $data);
        if (empty($response)) {
            ResponseHelper::sendErrorResponse("Failed to edit category", 500);
            return;
        }

        ResponseHelper::sendSuccessResponse(null, 'Category edited successfully', 201);
        return;
    }

    public function deleteCategory($param)
    {
        if (!is_array($param) || !isset($param['id']) || empty($param)) {
            ResponseHelper::sendErrorResponse("Invalid data or data is empty", 400);
            return;
        }

        HeaderHelper::setHeaders();

        $response = $this->categories_model->deleteCategory($param['id']);

        if (!$response) {
            ResponseHelper::sendErrorResponse("Failed to delete category", 500);
            return;
        }

        ResponseHelper::sendSuccessResponse(null, 'Category deleted successfully');
        return;
    }
}
