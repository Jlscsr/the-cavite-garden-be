<?php

namespace App;

class Routes
{
    private $routes;

    public function __construct()
    {
        $this->routes = [
            "api/user/info" => [
                "handler" => "AuthenticationController@getUserInfo",
                "middleware" => true,
                "requiredRole" => "both"
            ],
            /* TODO: Need some fix for transactions API routes */
            "api/transactions/:status" => [
                "handler" => "TransactionController@getAllTransactions",
                "middleware" => true,
                "requiredRole" => "admin"
            ],
            "api/transaction/status/:id" => [
                "handler" => "TransactionController@updateTransactionStatus",
                "middleware" => true,
                "requiredRole" => "admin"
            ],
            "api/transactions/customer/:id" => [
                "handler" => "TransactionController@getTransactionByCustomerID",
                "middleware" => true,
                "requiredRole" => "customer"
            ],
            "api/transaction/add" => [
                "handler" => "TransactionController@addNewTransaction",
                "middleware" => true,
                "requiredRole" => "customer"
            ],
            /* Customer Cart API routes */
            "api/customer/cart/delete/:id" => [
                "handler" => "CartController@deleteProductFromCart",
                "middleware" => true,
                "requiredRole" => "customer"
            ],
            "api/customer/cart/add" => [
                "handler" => "CartController@addProductToCart",
                "middleware" => true,
                "requiredRole" => "customer"
            ],
            "api/customer/cart" => [
                "handler" => "CartController@getCostumerCartProducts",
                "middleware" => true,
                "requiredRole" => "customer"
            ],
            /* Customers API routes */
            "api/customer/address/add" => [
                "handler" => "CustomersController@addNewUserAddress",
                "middleware" => true,
                "requiredRole" => "customer"
            ],
            "api/customer/id" => [
                "handler" => "CustomersController@getCustomerById",
                "middleware" => true,
                "requiredRole" => "both"
            ],
            "api/customers" => [
                "handler" => "CustomersController@getAllCustomers",
                "middleware" => true,
                "requiredRole" => "admin"
            ],
            /* Employees API route */
            "api/employee/edit/:id" => [
                "handler" => "EmployeesController@editEmployee",
                "middleware" => true,
                "requiredRole" => "admin"
            ],
            "api/employee/add" => [
                "handler" => "EmployeesController@addNewEmployee",
                "middleware" => true,
                "requiredRole" => "admin"
            ],
            "api/employee/id" => [
                "handler" => "EmployeesController@getEmployeeById",
                "middleware" => true,
                "requiredRole" => "admin"
            ],
            "api/employees" => [
                "handler" => "EmployeesController@getAllEmployees",
                "middleware" => true,
                "requiredRole" => "admin"
            ],
            /* Sub Categories API routes */
            "api/subcategory/delete/:id" => [
                "handler" => "SubCategoriesController@deleteSubCategory",
                "middleware" => true,
                "requiredRole" => "admin"
            ],
            "api/subcategory/edit/:id" => [
                "handler" => "SubCategoriesController@editSubCategory",
                "middleware" => true,
                "requiredRole" => "admin"
            ],
            "api/subcategory/add" => [
                "handler" => "SubCategoriesController@addNewSubCategory",
                "middleware" => true,
                "requiredRole" => "admin"
            ],
            "api/subcategories" => [
                "handler" => "SubCategoriesController@getAllSubCategories",
                "middleware" => true,
                "requiredRole" => "admin"
            ],
            /* Categories API routes */
            "api/category/edit/:id" => [
                "handler" => "CategoriesController@editCategory",
                "middleware" => true,
                "requiredRole" => "admin"
            ],
            "api/category/delete/:id" => [
                "handler" => "CategoriesController@deleteCategory",
                "middleware" => true,
                "requiredRole" => "admin"
            ],
            "api/category/add" => [
                "handler" => "CategoriesController@addNewCategory",
                "middleware" => true,
                "requiredRole" => "admin"
            ],
            "api/products/category/id/:id" => [
                "handler" => "ProductsController@getAllProductsByCategory",
                "middleware" => false,
                "requiredRole" => "both"
            ],
            "api/categories" => [
                "handler" => "CategoriesController@getAllCategories",
                "middleware" => false,
                "requiredRole" => "both"
            ],
            /* Products API routes */
            "api/product/delete/:id" => [
                "handler" => "ProductsController@deleteProduct",
                "middleware" => false
            ],
            "api/product/edit/:id" => [
                "handler" => "ProductsController@editProduct",
                "middleware" => true,
                "requiredRole" => "admin"
            ],
            "api/product/add" => [
                "handler" => "ProductsController@addNewProduct",
                "middleware" => true,
                "requiredRole" => "admin"
            ],
            "api/product/id/:id" => [
                "handler" => "ProductsController@getProductByID",
                "middleware" => true,
                "requiredRole" => "both"
            ],
            "api/products" => [
                "handler" => "ProductsController@getAllProducts",
                "middleware" => false,
                "requiredRole" => "both"
            ],
            /* Authentication API routes */
            "api/auth/logout" => [
                "handler" => "AuthenticationController@logout",
                "middleware" => false
            ],
            "api/auth/check" => [
                "handler" => "AuthenticationController@checkToken",
                "middleware" => false
            ],
            "api/auth/login" => [
                "handler" => "AuthenticationController@login",
                "middleware" => false
            ],
            "api/auth/register" => [
                "handler" => "AuthenticationController@register",
                "middleware" => false,
            ],
        ];
    }

    /**
     * Get the route based on the URL request and handle dynamic parameters.
     *
     * @param string $urlRequest The URL request to match against routes
     * @return ?array The route information or null if no match is found
     */
    public function getRoute(string $urlRequest): ?array
    {
        foreach ($this->routes as $route => $handler) {
            // Check for direct match
            if ($route === $urlRequest) {
                return $handler;
            }

            // Check for dynamic parameters
            $routeParts = explode('/', $route);
            $requestParts = explode('/', $urlRequest);

            if (count($routeParts) === count($requestParts)) {
                $params = [];
                for ($i = 0; $i < count($routeParts); $i++) {
                    if (strpos($routeParts[$i], ':') === 0) {
                        $parameterName = substr($routeParts[$i], 1);
                        $params[$parameterName] = $requestParts[$i];
                    } else if ($routeParts[$i] !== $requestParts[$i]) {
                        break;
                    }
                }

                if (!empty($params)) {
                    return [
                        'handler' => $handler,
                        'params' => $params
                    ];
                }
            }
        }

        return null;
    }
}
