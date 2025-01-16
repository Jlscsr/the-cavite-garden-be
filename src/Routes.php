<?php

namespace App;

class Routes
{
    private $routes;

    public function __construct()
    {
        $this->routes = [

            /* Reports API routes */
            "/api/reports" => [
                "handler" => "ReportsController@getAllReports",
                "middleware" => false,
                "requiredRole" => "admin"
            ],

            /* Refund API routes */

            "/api/refund/transaction/status/update/:id" => [
                "handler" => "RefundController@updateRefundTransactionStatus",
                "middleware" => false,
                "requiredRole" => "admin"
            ],

            "/api/refund/transaction/add" => [
                "handler" => "RefundController@addNewRefundTransaction",
                "middleware" => false,
                "requiredRole" => "admin"
            ],

            "/api/refund/transactions/status/:status" => [
                "handler" => "RefundController@getAllRefundTransactions",
                "middleware" => false,
                "requiredRole" => "admin"
            ],

            /* Reivews API  routes */
            "/api/product/review/reply/add" => [
                "handler" => "ReviewsController@addReviewReply",
                "middleware" => false,
                "requiredRole" => "admin"
            ],
            "/api/product/review/delete/:id" => [
                "handler" => "ReviewsController@deleteReview",
                "middleware" => false,
                "requiredRole" => "admin"
            ],
            "/api/product/review/add" => [
                "handler" => "ReviewsController@addNewProductReview",
                "middleware" => false,
                "requiredRole" => "both"
            ],
            '/api/reviews' => [
                'handler' => 'ReviewsController@getAllReviews',
                'middleware' => false,
                'requiredRole' => 'both'
            ],
            /* User API routes */
            "/api/user/delete/:id" => [
                "handler" => "CustomersController@deleteUserAccount",
                "middleware" => false,
                "requiredRole" => "admin"
            ],
            "/api/user/info" => [
                "handler" => "AuthenticationController@getUserInfo",
                "middleware" => false,
                "requiredRole" => "both"
            ],
            "/api/transactions/orderPurpose/status/:orderPurpose/:status" => [
                "handler" => "TransactionController@getAllTransactions",
                "middleware" => false,
                "requiredRole" => "admin"
            ],
            '/api/transaction/orderPurpose/update/:id' => [
                'handler' => 'TransactionController@updateTransactionOrderPurpose',
                'middleware' => false,
                'requiredRole' => 'admin'
            ],
            "/api/transaction/status/update/:id" => [
                "handler" => "TransactionController@updateTransactionStatus",
                "middleware" => false,
                "requiredRole" => "admin"
            ],
            "/api/transactions/customer/id/:customerID" => [
                "handler" => "TransactionController@getTransactionByCustomerID",
                "middleware" => false,
                "requiredRole" => "customer"
            ],
            "/api/transaction/add" => [
                "handler" => "TransactionController@addNewTransaction",
                "middleware" => false,
                "requiredRole" => "customer"
            ],
            /* Customer Cart API routes */
            "/api/customer/cart/delete/:id" => [
                "handler" => "CartController@deleteProductFromCart",
                "middleware" => false,
                "requiredRole" => "customer"
            ],
            "/api/customer/cart/add" => [
                "handler" => "CartController@addProductToCart",
                "middleware" => false,
                "requiredRole" => "customer"
            ],
            "/api/customer/cart/id/:id" => [
                "handler" => "CartController@getProductCartByID",
                "middleware" => false,
                "requiredRole" => "customer"
            ],
            "/api/customer/cart" => [
                "handler" => "CartController@getCostumerCartProducts",
                "middleware" => false,
                "requiredRole" => "customer"
            ],
            /* Customers API routes */
            "/api/customer/address/id/delete/:id" => [
                "handler" => "CustomersController@deleteUserAddress",
                "middleware" => false,
                "requiredRole" => "customer"
            ],
            '/api/customer/address/id/update/:id' => [
                'handler' => 'CustomersController@updateCustomerAddress',
                'middleware' => false,
                'requiredRole' => 'customer'
            ],
            "/api/customer/address/add" => [
                "handler" => "CustomersController@addNewUserAddress",
                "middleware" => false,
                "requiredRole" => "customer"
            ],
            "/api/customer/update" => [
                "handler" => "CustomersController@updateUserData",
                "middleware" => false,
                "requiredRole" => "customer"
            ],
            "/api/customer/id" => [
                "handler" => "CustomersController@getCustomerById",
                "middleware" => false,
                "requiredRole" => "both"
            ],
            "/api/customers" => [
                "handler" => "CustomersController@getAllCustomers",
                "middleware" => false,
                "requiredRole" => "admin"
            ],
            /* Employees API route */
            "/api/employee/edit/:id" => [
                "handler" => "EmployeesController@editEmployee",
                "middleware" => false,
                "requiredRole" => "admin"
            ],
            "/api/employee/add" => [
                "handler" => "EmployeesController@addNewEmployee",
                "middleware" => false,
                "requiredRole" => "admin"
            ],
            "/api/employee/id" => [
                "handler" => "EmployeesController@getEmployeeById",
                "middleware" => false,
                "requiredRole" => "admin"
            ],
            "/api/employees" => [
                "handler" => "EmployeesController@getAllEmployees",
                "middleware" => false,
                "requiredRole" => "admin"
            ],
            /* Sub Categories API routes */
            "/api/subcategory/delete/:id" => [
                "handler" => "SubCategoriesController@deleteSubCategory",
                "middleware" => false,
                "requiredRole" => "admin"
            ],
            "/api/subcategory/edit/:id" => [
                "handler" => "SubCategoriesController@editSubCategory",
                "middleware" => false,
                "requiredRole" => "admin"
            ],
            "/api/subcategory/add" => [
                "handler" => "SubCategoriesController@addNewSubCategory",
                "middleware" => false,
                "requiredRole" => "admin"
            ],
            "/api/subcategories" => [
                "handler" => "SubCategoriesController@getAllSubCategories",
                "middleware" => false,
                "requiredRole" => "admin"
            ],
            /* Categories API routes */
            "/api/category/edit/:id" => [
                "handler" => "CategoriesController@editCategory",
                "middleware" => false,
                "requiredRole" => "admin"
            ],
            "/api/category/delete/:id" => [
                "handler" => "CategoriesController@deleteCategory",
                "middleware" => false,
                "requiredRole" => "admin"
            ],
            "/api/category/add" => [
                "handler" => "CategoriesController@addNewCategory",
                "middleware" => false,
                "requiredRole" => "admin"
            ],
            "/api/products/category/id/:id" => [
                "handler" => "ProductsController@getAllProductsByCategory",
                "middleware" => false,
                "requiredRole" => "both"
            ],
            "/api/categories" => [
                "handler" => "CategoriesController@getAllCategories",
                "middleware" => false,
                "requiredRole" => "both"
            ],
            /* Products API routes */
            "/api/product/delete/:id" => [
                "handler" => "ProductsController@deleteProduct",
                "middleware" => false
            ],
            "/api/product/edit/:id" => [
                "handler" => "ProductsController@editProduct",
                "middleware" => false,
                "requiredRole" => "admin"
            ],
            "/api/product/add" => [
                "handler" => "ProductsController@addNewProduct",
                "middleware" => false,
                "requiredRole" => "admin"
            ],
            "/api/product/id/:id" => [
                "handler" => "ProductsController@getProductByID",
                "middleware" => false,
                "requiredRole" => "both"
            ],
            "/api/products" => [
                "handler" => "ProductsController@getAllProducts",
                "middleware" => false,
                "requiredRole" => "both"
            ],
            /* Authentication API routes */
            "/api/auth/logout" => [
                "handler" => "AuthenticationController@logout",
                "middleware" => false
            ],
            "/api/auth/check" => [
                "handler" => "AuthenticationController@checkToken",
                "middleware" => false
            ],
            "/api/auth/login" => [
                "handler" => "AuthenticationController@login",
                "middleware" => false
            ],
            "/api/auth/register" => [
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
