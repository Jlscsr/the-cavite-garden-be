<?php

namespace App\Controllers;

use InvalidArgumentException;
use RuntimeException;

use App\Models\ProductsModel;

use App\Validators\ProductsValidator;

use App\Helpers\ResponseHelper;

class ProductsController
{
    private $productsModel;

    /**
     * Constructor for the class.
     *
     * @param PDO $pdo The PDO object for database connection.
     * @return void
     */
    public function __construct($pdo)
    {
        $this->productsModel = new ProductsModel($pdo);
    }

    /**
     * Retrieves all products from the database.
     *
     * This function calls the `getAllProducts` method of the `$productsModel` object to fetch all products from the database.
     * If the result is empty, it sends a 404 error response with the message "No products found".
     * Otherwise, it sends a success response with the retrieved products and the message "Products retrieved successfully".
     *
     * @throws RuntimeException If an error occurs during the retrieval of products.
     * @return void
     */
    public function getAllProducts()
    {
        try {
            $products = $this->productsModel->getAllProducts();


            if (empty($products)) {
                return ResponseHelper::sendSuccessResponse([], "No products found", 404);
            }

            return ResponseHelper::sendSuccessResponse($products, 'Products retrieved successfully');
        } catch (RuntimeException $e) {
            return ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Retrieves a product by ID from the database.
     *
     * This function validates the parameter passed to it using the ProductsValidator class. It then retrieves the product
     * from the database that has the specified ID using the ProductsModel class. If no product is found, a 404 error response
     * is sent with a message indicating that no product was found. If a product is found, a success response is sent with
     * the retrieved product and a message indicating that the product was retrieved successfully.
     *
     * @param array $parameter The parameter containing the ID of the product.
     * @throws RuntimeException If an error occurs during the retrieval of the product.
     * @throws InvalidArgumentException If the parameter is invalid.
     * @return void
     */
    public function getProductByID(array $parameter)
    {
        try {
            ProductsValidator::validateGetProductRequestsByParameter($parameter);

            $product = $this->productsModel->getProductByID($parameter['id']);

            if (empty($product)) {
                return ResponseHelper::sendSuccessResponse([], "No product found by that id.", 404);
            }

            return ResponseHelper::sendSuccessResponse($product, 'Product retrieved successfully');
        } catch (RuntimeException $e) {
            return ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        } catch (InvalidArgumentException $e) {
            return ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Retrieves all products by category from the database.
     *
     * This function validates the parameter passed to it using the ProductsValidator class. It then retrieves all products
     * from the database that belong to the specified category using the ProductsModel class. If no products are found, a
     * 404 error response is sent with a message indicating that no products were found. If products are found, a success
     * response is sent with the retrieved products and a message indicating that the products were retrieved successfully.
     *
     * @param array $parameter The parameter containing the name of the category.
     * @throws RuntimeException If an error occurs during the retrieval of products.
     * @throws InvalidArgumentException If the parameter is invalid.
     * @return void
     */
    public function getAllProductsByCategory(array $parameter)
    {
        try {
            ProductsValidator::validateGetProductRequestsByParameter($parameter);

            $products = $this->productsModel->getAllProductsByCategory($parameter['id']);

            if (empty($products)) {
                return ResponseHelper::sendSuccessResponse([], "No Products found base on the Category", 404);
            }

            return ResponseHelper::sendSuccessResponse($products, 'Products retrieved successfully');
        } catch (RuntimeException $e) {
            return ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        } catch (InvalidArgumentException $e) {
            return ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Adds a new product to the database.
     *
     * This function validates the payload passed to it using the ProductsValidator class. It then adds the new product
     * to the database using the ProductsModel class. If the product is successfully added, a success response is sent
     * with a message indicating that the product was added successfully. If the product fails to be added, a 500 error
     * response is sent with a message indicating that the product failed to be added.
     *
     * @param array $payload The payload containing the details of the product to be added.
     * @throws RuntimeException If an error occurs during the addition of the product.
     * @throws InvalidArgumentException If the payload is invalid.
     * @return void
     */
    public function addNewProduct(array $payload)
    {
        try {
            ProductsValidator::validateAddProductRequest($payload);

            $response = $this->productsModel->addNewProduct($payload);

            if (!$response) {
                return ResponseHelper::sendErrorResponse("Failed to add new product", 400);
            }

            return ResponseHelper::sendSuccessResponse([], 'Product added successfully', 201);
        } catch (RuntimeException $e) {
            return ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        } catch (InvalidArgumentException $e) {
            return ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Edits a product in the database.
     *
     * This function validates the edit product request using the ProductsValidator class.
     * It then edits the product in the database using the ProductsModel class.
     * If the product is successfully edited, a success response is sent with a message indicating that the product was edited successfully.
     * If the product fails to be edited, a 500 error response is sent with a message indicating that the product failed to be edited.
     *
     * @param array $parameter The parameter containing the ID of the product to be edited.
     * @param array $payload The payload containing the details of the product to be edited.
     * @throws RuntimeException If an error occurs during the editing of the product.
     * @throws InvalidArgumentException If the parameter or payload is invalid.
     * @return void
     */
    public function editProduct(array $parameter, array $payload)
    {
        try {
            ProductsValidator::validateEditProductRequest($parameter, $payload);

            $productID = $parameter['id'];
            $response = $this->productsModel->editProduct($productID, $payload);

            if (!$response) {
                return ResponseHelper::sendErrorResponse("Failed to edit product", 400);
            }

            return ResponseHelper::sendSuccessResponse([], 'Product edited successfully', 201);
        } catch (RuntimeException $e) {
            return ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Deletes a product in the database.
     *
     * This function validates the delete product request using the ProductsValidator class.
     * It then deletes the product in the database using the ProductsModel class.
     * If the product is successfully deleted, a success response is sent with a message indicating that the product was deleted successfully.
     * If the product fails to be deleted, a 500 error response is sent with a message indicating that the product failed to be deleted.
     *
     * @param array $parameter The parameter containing the ID of the product to be deleted.
     * @throws RuntimeException If an error occurs during the deletion of the product.
     * @return void
     */
    public function deleteProduct(array $parameter)
    {
        try {
            ProductsValidator::validateDeleteProductRequest($parameter);

            $response = $this->productsModel->deleteProduct($parameter['id']);

            if (!$response) {
                return ResponseHelper::sendErrorResponse("Failed to delete product", 400);
            }

            return ResponseHelper::sendSuccessResponse([], 'Product deleted successfully');
        } catch (RuntimeException $e) {
            return ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }
}
