<?php

use App\Models\CategoriesModel;

use App\Validators\CategoriesValidator;

use App\Helpers\ResponseHelper;

class CategoriesController
{
    private $categoriesModel;


    public function __construct($pdo)
    {
        $this->categoriesModel = new CategoriesModel($pdo);
    }

    /**
     * Retrieves all categories from the database and sends the response.
     *
     * @return void
     * @throws RuntimeException if there is an error retrieving the categories
     */
    public function getAllCategories()
    {
        try {
            $productCategories = $this->categoriesModel->getAllCategories();

            if (empty($productCategories)) {
                return ResponseHelper::sendSuccessResponse([], "No categories found", 404);
            }

            return ResponseHelper::sendSuccessResponse($productCategories, 'Product categories retrieved successfully');
        } catch (RuntimeException $e) {
            return ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Adds a new category to the database.
     *
     * @param array $payload The data for the new category.
     * @throws RuntimeException If there is an error retrieving the categories.
     * @throws InvalidArgumentException If the request payload is invalid.
     * @return void
     */
    public function addNewCategory(array $payload)
    {
        try {
            CategoriesValidator::validateAddCategoryPayload($payload);

            $response = $this->categoriesModel->addNewCategory($payload);

            if (!$response) {
                return ResponseHelper::sendErrorResponse("Failed to add new Category", 400);
            }

            return ResponseHelper::sendSuccessResponse([], 'Category added successfully', 201);
        } catch (RuntimeException $e) {
            return ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        } catch (InvalidArgumentException $e) {
            return ResponseHelper::sendErrorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Edit a category based on the provided parameters and payload.
     *
     * @param array $parameter The parameters for editing the category.
     * @param array $payload The data to update the category.
     * @throws RuntimeException If there is an error during category editing.
     * @throws InvalidArgumentException If the category update request is invalid.
     * @return void
     */
    public function editCategory(array $parameter, array $payload)
    {
        try {
            CategoriesValidator::validateEditCategoryRequest($parameter, $payload);

            $response = $this->categoriesModel->editCategory($parameter['id'], $payload);

            if (empty($response)) {
                return ResponseHelper::sendErrorResponse("Failed to edit category", 400);
            }

            return ResponseHelper::sendSuccessResponse([], 'Category edited successfully', 201);
        } catch (RuntimeException $e) {
            return ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        } catch (InvalidArgumentException $e) {
            return ResponseHelper::sendErrorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Deletes a category based on the provided parameters.
     *
     * @param array $parameter The parameters for deleting the category.
     * @throws RuntimeException If there is an error during category deletion.
     * @throws InvalidArgumentException If the category deletion request is invalid.
     * @return void
     */
    public function deleteCategory(array $parameter)
    {
        try {
            CategoriesValidator::validateDeleteCategoryRequest($parameter);

            $response = $this->categoriesModel->deleteCategory($parameter['id']);

            if (!$response) {
                return ResponseHelper::sendErrorResponse("Failed to delete category", 400);
            }

            return ResponseHelper::sendSuccessResponse([], 'Category deleted successfully');
        } catch (RuntimeException $e) {
            return ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        } catch (InvalidArgumentException $e) {
            return ResponseHelper::sendErrorResponse($e->getMessage(), 400);
        }
    }
}
