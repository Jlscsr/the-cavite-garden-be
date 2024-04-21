<?php

use Helpers\ResponseHelper;
use Helpers\HeaderHelper;

use Models\SubCategoriesModel;

class SubCategoriesController
{
    private $sub_categories_model;

    public function __construct($pdo)
    {
        $this->sub_categories_model = new SubCategoriesModel($pdo);
    }

    /**
     * Get all plant types and return them as JSON.
     */
    public function getAllPlantSubCategories()
    {
        HeaderHelper::setHeaders();

        $product_categories = $this->sub_categories_model->getAllPlantSubCategories();

        if (empty($product_categories)) {
            ResponseHelper::sendErrorResponse("No plant types found", 404);
            return;
        }

        ResponseHelper::sendSuccessResponse($product_categories, 'Plant categories retrieved successfully');
        return;
    }

    public function addNewSubCategory($data)
    {
        if (!is_array($data) || empty($data)) {
            ResponseHelper::sendErrorResponse("Invalid parameter type.", 400);
            return;
        }

        HeaderHelper::setHeaders();

        $response = $this->sub_categories_model->addNewSubCategory($data);

        if (empty($response)) {
            ResponseHelper::sendErrorResponse("Failed to add Sub Category", 500);
            return;
        }

        ResponseHelper::sendSuccessResponse(null, 'Sub Category added successfully', 201);
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
