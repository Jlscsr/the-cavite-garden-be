<?php

use Helpers\ResponseHelper;

use Validators\SubCategoriesValidator;

use Models\SubCategoriesModel;

class SubCategoriesController
{
    private $subCategoriesModel;

    public function __construct($pdo)
    {
        $this->subCategoriesModel = new SubCategoriesModel($pdo);
    }

    /**
     * Retrieves all subcategories from the subCategoriesModel and sends a success response with the retrieved data.
     * If no subcategories are found, sends an error response with a 404 status code.
     * If an exception occurs, sends an error response with a 500 status code.
     *
     * @throws RuntimeException if an exception occurs while retrieving the subcategories.
     * @return void
     */
    public function getAllSubCategories(): void
    {
        try {
            $subCategories = $this->subCategoriesModel->getAllSubCategories();

            if (empty($subCategories)) {
                ResponseHelper::sendErrorResponse("No Sub Categories found", 404);
                exit;
            }

            ResponseHelper::sendSuccessResponse($subCategories, 'Sub Categories retrieved successfully');
        } catch (RuntimeException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Adds a new subcategory to the subCategoriesModel.
     *
     * @param array $payload The data for the new subcategory.
     * @throws RuntimeException If an exception occurs while adding the subcategory.
     * @throws InvalidArgumentException If the request payload is invalid.
     * @return void
     */
    public function addNewSubCategory(array $payload): void
    {
        try {
            SubCategoriesValidator::validateAddSubCategoryRequest($payload);

            $response = $this->subCategoriesModel->addNewSubCategory($payload);

            if (!$response) {
                ResponseHelper::sendErrorResponse("Failed to add Sub Category", 400);
                exit;
            }

            ResponseHelper::sendSuccessResponse([], 'Sub Category added successfully', 201);
        } catch (RuntimeException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        } catch (InvalidArgumentException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Edits a subcategory based on the provided parameters and payload.
     *
     * @param array $parameter The parameters for editing the subcategory.
     * @param array $payload The data to update the subcategory with.
     * @throws RuntimeException If an error occurs during the editing process.
     * @throws InvalidArgumentException If the provided arguments are invalid.
     * @return void
     */
    public function editSubCategory(array $parameter, array $payload): void
    {
        try {
            SubCategoriesValidator::validateEditSubCategoryRequest($parameter, $payload);

            $response = $this->subCategoriesModel->editSubCategory((int) $parameter['id'], $payload);

            if (!$response) {
                ResponseHelper::sendErrorResponse("Failed to edit Sub Category", 400);
                exit;
            }

            ResponseHelper::sendSuccessResponse([], 'Sub Category edited successfully', 201);
        } catch (RuntimeException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        } catch (InvalidArgumentException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Deletes a subcategory based on the provided parameters.
     *
     * @param array $parameter The parameters for deleting the subcategory.
     * @throws RuntimeException If an error occurs during the deletion process.
     * @throws InvalidArgumentException If the provided arguments are invalid.
     * @return void
     */
    public function deleteSubCategory(array $parameter): void
    {
        try {
            SubCategoriesValidator::validateDeleteSubCategoryRequest($parameter);

            $response = $this->subCategoriesModel->deleteSubCategory((int) $parameter['id']);

            if (!$response) {
                ResponseHelper::sendErrorResponse("Failed to delete Sub Category", 500);
                exit;
            }

            ResponseHelper::sendSuccessResponse([], 'Sub Category deleted successfully', 201);
        } catch (RuntimeException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        } catch (InvalidArgumentException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 400);
        }
    }
}
