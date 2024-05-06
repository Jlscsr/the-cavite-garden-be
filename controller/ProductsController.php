<?php


use Helpers\ResponseHelper;
use Helpers\HeaderHelper;

use Models\ProductsModel;
use Models\CategoriesModel;
use Models\SubCategoriesModel;

class ProductsController
{
    private $productsModel;
    private $categoriesModel;
    private $subCategoriesModel;

    /**
     * Constructor for the class.
     *
     * @param PDO $pdo The PDO object for database connection.
     * @return void
     */
    public function __construct($pdo)
    {
        $this->categoriesModel = new CategoriesModel($pdo);
        $this->subCategoriesModel = new SubCategoriesModel($pdo);
        $this->productsModel = new ProductsModel($pdo);

        HeaderHelper::setResponseHeaders();
    }


    /**
     * Retrieves all products from the database.
     *
     * This function queries the database to retrieve all products and returns them as an array.
     * If no products are found, it sends a 404 error response.
     * If there is an error during the database operation, it sends a 500 error response.
     *
     * @return void
     * @throws RuntimeException if there is an error during the database operation
     */
    public function getAllProducts()
    {

        try {
            $products = $this->productsModel->getAllProducts();

            if (empty($products)) {
                ResponseHelper::sendErrorResponse("No products found", 404);
                return;
            }

            $products = $this->addCategoryAndSubCategoryNamesToProducts($products);

            ResponseHelper::sendSuccessResponse($products, 'Products retrieved successfully');
            return;
        } catch (RuntimeException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }


    /**
     * Retrieves a product by its ID from the database.
     *
     * @param array $param The parameter containing the product ID.
     * @throws RuntimeException If there is an error during the database operation.
     * @return void
     */
    public function getProductByID($param)
    {

        if (!isset($param['id']) && empty($param['id'])) {
            ResponseHelper::sendErrorResponse("Invalid parameter type.", 400);
            return;
        }

        $productID = (int) $param['id'];

        try {

            $products = $this->productsModel->getProductByID($productID);

            if (empty($products)) {
                ResponseHelper::sendErrorResponse("No products found by that id.", 404);
                return;
            }

            $productCategory = $this->categoriesModel->getCategoryById($products['categoryId']);
            $productSubCategory = $this->subCategoriesModel->getSubCategoryById($products['subCategoryId']);

            $products['category_name'] = !empty($productCategory) ? $productCategory[0]['name'] : null;
            $products['sub_category_name'] = !empty($productSubCategory) ? $productSubCategory[0]['name'] : null;

            ResponseHelper::sendSuccessResponse($products, 'Products retrieved successfully');
        } catch (RuntimeException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }


    /**
     * Retrieves all products from the database based on the given category ID.
     *
     * @param array $param The parameter containing the category ID.
     *                    The 'id' key should be set and not empty.
     * @throws RuntimeException If there is an error during the database operation.
     * @return void
     */
    public function getAllProductsByCategory($param)
    {

        if (!isset($param['id']) && empty($param['id'])) {
            ResponseHelper::sendErrorResponse("Invalid plant type.", 400);
            return;
        }

        $categoryID = (int) $param['id'];

        try {
            $products = $this->productsModel->getAllProductsByCategory($categoryID);

            if (empty($products)) {
                ResponseHelper::sendErrorResponse("No Products found base on the Category");
                return;
            }

            $products = $this->addCategoryAndSubCategoryNamesToProducts($products);

            ResponseHelper::sendSuccessResponse($products, 'Products retrieved successfully');
        } catch (RuntimeException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }


    /**
     * Adds a new product to the database.
     *
     * @param array $payload The payload containing the product data.
     * @throws RuntimeException If there is an error during the database operation.
     * @return void
     */
    public function addNewProduct($payload)
    {
        if (!is_array($payload) && empty($payload)) {
            ResponseHelper::sendErrorResponse("Invalid payload or payload is empty", 400);
            return;
        }

        try {
            $response = $this->productsModel->addNewProduct($payload);

            if (empty($response)) {
                ResponseHelper::sendErrorResponse("Failed to add new product", 500);
                return;
            }

            ResponseHelper::sendSuccessResponse([], 'Product added successfully', 201);
        } catch (RuntimeException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }


    /**
     * Edits a product in the database.
     *
     * @param array $param An associative array containing the product ID.
     * @param array $payload An associative array containing the updated product data.
     * @throws RuntimeException If there is an error during the database operation.
     * @return void
     */
    public function editProduct($param, $payload)
    {

        if (!is_array($payload) || empty($payload) || !isset($param['id']) || empty($param['id'])) {
            ResponseHelper::sendErrorResponse("Invalid payload or payload is empty", 400);
            return;
        }

        try {
            $productID = (int) $param['id'];

            $response = $this->productsModel->editProduct($productID, $payload);

            if (empty($response)) {
                ResponseHelper::sendErrorResponse("Failed to edit product", 500);
                return;
            }

            ResponseHelper::sendSuccessResponse(null, 'Product edited successfully', 201);
        } catch (RuntimeException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Deletes a product from the database based on the provided ID.
     *
     * @param array $param An associative array containing the ID of the product to be deleted.
     *                     The array must have a key 'id' with a non-empty value.
     * @throws RuntimeException If there is an error during the database operation.
     * @return void
     */
    public function deleteProduct($param)
    {
        if (!is_array($param) || !isset($param['id']) || empty($param)) {
            ResponseHelper::sendErrorResponse("Invalid data or data is empty", 400);
            return;
        }

        try {
            $response = $this->productsModel->deleteProduct($param['id']);

            if (!$response) {
                ResponseHelper::sendErrorResponse("Failed to delete product", 500);
                return;
            }

            ResponseHelper::sendSuccessResponse(null, 'Product deleted successfully');
        } catch (RuntimeException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }


    /**
     * Adds category and subcategory names to products.
     *
     * @param array $products The array of products.
     * @return array The modified array of products with category and subcategory names added.
     */
    private function addCategoryAndSubCategoryNamesToProducts($products)
    {
        $productsCopy = $products;

        foreach ($productsCopy as $key => &$value) {
            $category = $this->categoriesModel->getCategoryById($value['categoryId']);
            $value['category_name'] = $category[0]['name'];
        }
        unset($value);

        foreach ($productsCopy as $key => &$value) {
            $sub_category = $this->subCategoriesModel->getSubCategoryById($value['subCategoryId']);

            if (!empty($sub_category)) {
                $value['sub_category_name'] = $sub_category[0]['name'];
            }
        }
        unset($value);

        return $productsCopy;
    }
}
