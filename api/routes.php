<?php
require_once dirname(__DIR__) . '/helpers/HeaderHelper.php';

class Route
{

    public function __construct()
    {
        $this->routes = [
            "api/user/address/add" => [
                "handler" => "AccountController@addNewUserAddress",
                "middleware" => false
            ],
            "api/user/info" => [
                "handler" => "AccountController@getAccountById",
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
                "middleware" => [
                    'required' => true,
                    'handler' => 'TransactionMiddleware'
                ],
            ],
            "api/cart/delete/:id" => [
                "handler" => "CartController@deleteProductFromCart",
                "middleware" => [
                    'required' => true,
                    'handler' => 'CartMiddleware'
                ]
            ],
            "api/cart/add" => [
                "handler" => "CartController@addToCart",
                "middlware" => [
                    'required' => true,
                    'handler' => 'CartMiddleware'
                ],
            ],
            "api/cart" => [
                "handler" => "CartController@getCostumerCartProducts",
                "middleware" => [
                    'required' => true,
                    'handler' => 'CartMiddleware'
                ],
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
                "middleware" =>  [
                    'required' => true,
                    'handler' => 'CategoriesMiddleware'
                ],
            ],
            "api/category/delete/:id" => [
                "handler" => "CategoriesController@deleteCategory",
                "middleware" =>  [
                    'required' => true,
                    'handler' => 'CategoriesMiddleware'
                ],
            ],
            "api/plant/category/:id" => [
                "handler" => "PlantController@getAllPlantsByCategory",
                "middleware" => false
            ],
            "api/plant/delete/:id" => [
                "handler" => "PlantController@deletePlant",
                "middleware" =>  [
                    'required' => true,
                    'handler' => 'PlantMiddleware'
                ],
            ],
            "api/plant/edit/:id" => [
                "handler" => "PlantController@editPlant",
                "middleware" => [
                    'required' => true,
                    'handler' => 'PlantMiddleware'
                ],
            ],
            "api/category/add" => [
                "handler" => "CategoriesController@addNewCategory",
                "middleware" => false,
            ],
            "api/plant/add" => [
                "handler" => "PlantController@addNewPlant",
                "middleware" => [
                    'required' => true,
                    'handler' => 'PlantMiddleware'
                ],
            ],
            "api/plant/:id" => [
                "handler" => "PlantController@getPlantById",
                "middleware" => false,
            ],
            "api/categories" => [
                "handler" => "CategoriesController@getAllPlantCategories",
                "middleware" => false,
            ],
            "api/plant" => [
                "handler" => "PlantController@getAllPlants",
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
