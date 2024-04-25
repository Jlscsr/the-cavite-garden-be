<?php

class Route
{
    private $routes;

    public function __construct()
    {
        $this->routes = [
            "api/subcategory/add" => [
                "handler" => "SubCategoriesController@addNewSubCategory",
                "middleware" => false
            ],
            "api/employees/edit/:id" => [
                "handler" => "EmployeesController@editEmployee",
                "middleware" => false
            ],
            "api/employees/add" => [
                "handler" => "EmployeesController@addNewEmployee",
                "middleware" => false
            ],
            "api/employee/info" => [
                "handler" => "EmployeesController@getEmployeeById",
                "middleware" => false
            ],
            "api/employees" => [
                "handler" => "EmployeesController@getAllEmployees",
                "middleware" => false
            ],
            "api/customer/address/add" => [
                "handler" => "CustomersController@addNewUserAddress",
                "middleware" => false
            ],
            "api/customer/info" => [
                "handler" => "CustomersController@getCustomerById",
                "middleware" => false
            ],
            "api/customers" => [
                "handler" => "CustomersController@getAllCustomers",
                "middleware" => false
            ],
            "api/transactions/:status" => [
                "handler" => "TransactionController@getAllTransactions",
                "middleware" => false
            ],
            "api/transaction/status/:id" => [
                "handler" => "TransactionController@updateTransactionStatus",
                "middleware" => false
            ],
            "api/transaction/add" => [
                "handler" => "TransactionController@addNewTransaction",
                "middleware" => false
            ],
            "api/cart/delete/:id" => [
                "handler" => "CartController@deleteProductFromCart",
                "middleware" => false
            ],
            "api/cart/add" => [
                "handler" => "CartController@addToCart",
                "middleware" => false
            ],
            "api/cart" => [
                "handler" => "CartController@getCostumerCartProducts",
                "middleware" => false
            ],
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
            "api/category/edit/:id" => [
                "handler" => "CategoriesController@editCategory",
                "middleware" => false
            ],
            "api/category/delete/:id" => [
                "handler" => "CategoriesController@deleteCategory",
                "middleware" => false
            ],
            "api/product/category/:id" => [
                "handler" => "ProductsController@getAllProductsByCategory",
                "middleware" => false
            ],
            "api/product/delete/:id" => [
                "handler" => "ProductsController@deleteProduct",
                "middleware" => false
            ],
            "api/product/edit/:id" => [
                "handler" => "ProductsController@editProduct",
                "middleware" => false
            ],
            "api/category/add" => [
                "handler" => "CategoriesController@addNewCategory",
                "middleware" => false,
            ],
            "api/product/add" => [
                "handler" => "ProductsController@addNewProduct",
                "middleware" => false
            ],
            "api/product/:id" => [
                "handler" => "ProductsController@getProductByID",
                "middleware" => false,
            ],
            "api/categories" => [
                "handler" => "CategoriesController@getAllPlantCategories",
                "middleware" => false,
            ],
            "api/products" => [
                "handler" => "ProductsController@getAllProducts",
                "middleware" => false
            ],
        ];
    }

    public function get_route($url_request)
    {
        foreach ($this->routes as $route => $handler) {
            // Check for direct match
            if ($route === $url_request) {
                return $handler;
            }

            // Check for dynamic parameters
            $route_parts = explode('/', $route);
            $requests_parts = explode('/', $url_request);

            if (count($route_parts) === count($requests_parts)) {
                $params = [];
                for ($i = 0; $i < count($route_parts); $i++) {
                    if (strpos($route_parts[$i], ':') === 0) {
                        $param_name = substr($route_parts[$i], 1);
                        $params[$param_name] = $requests_parts[$i];
                    } else if ($route_parts[$i] !== $requests_parts[$i]) {
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
