<?php

// Helpers
use Helpers\JWTHelper;
use Helpers\ResponseHelper;
use Helpers\HeaderHelper;
use Helpers\CookieManager;

// Models
use Models\CartModel;

class CartController
{
    private $jwt;
    private $cart_model;
    private $cookie_manager;

    public function __construct($pdo)
    {
        $this->jwt = new JWTHelper();
        $this->cart_model = new CartModel($pdo);
        $this->cookie_manager = new CookieManager($this->jwt);

        HeaderHelper::setResponseHeaders();
    }

    public function addToCart($data)
    {
        if (!is_array($data) || empty($data)) {
            ResponseHelper::sendErrorResponse("Invalid data or data is empty", 400);
            return;
        }

        $this->cookie_manager->validateCookiePressence();

        $token = $this->cookie_manager->extractAccessTokenFromCookieHeader();
        $is_token_valid = $this->jwt->validateToken($token);

        if (!$is_token_valid) {
            ResponseHelper::sendUnauthorizedResponse('Unauthorized');
            return;
        }

        $decoded_token = $this->jwt->decodeJWTData($token);
        $data['customer_id'] = $decoded_token->id;

        $response = $this->cart_model->addProductToCart($data);

        if (!$response) {
            ResponseHelper::sendErrorResponse($response, 500);
            return;
        }

        ResponseHelper::sendSuccessResponse([], 'Product added to cart successfully', 201);
    }

    public function getCostumerCartProducts()
    {


        $this->cookie_manager->validateCookiePressence();

        $token = $this->cookie_manager->extractAccessTokenFromCookieHeader();
        $is_token_valid = $this->jwt->validateToken($token);

        if (!$is_token_valid) {
            ResponseHelper::sendUnauthorizedResponse('Unauthorized');
            return;
        }

        $decoded_token = $this->jwt->decodeJWTData($token);
        $customer_id = $decoded_token->id;

        $response = $this->cart_model->getCostumerCartProducts($customer_id);

        if (empty($response)) {
            ResponseHelper::sendSuccessResponse([], 'No products in cart', 200);
            return;
        }

        ResponseHelper::sendSuccessResponse($response, 'Cart products retrieved successfully', 200);
    }

    public function deleteProductFromCart($param)
    {
        if (!is_array($param) || empty($param)) {
            ResponseHelper::sendErrorResponse("Invalid data or data is empty", 400);
            return;
        }

        $this->cookie_manager->validateCookiePressence();

        $token = $this->cookie_manager->extractAccessTokenFromCookieHeader();
        $is_token_valid = $this->jwt->validateToken($token);

        if (!$is_token_valid) {
            ResponseHelper::sendUnauthorizedResponse('Unauthorized');
            return;
        }

        $decoded_token = $this->jwt->decodeJWTData($token);
        $customer_id = $decoded_token->id;

        $cart_product_id = $param['id'];
        $cart_product_id = (int) $cart_product_id;


        $response = $this->cart_model->deleteProductFromCart($customer_id, $cart_product_id);

        ResponseHelper::sendSuccessResponse([], 'Product deleted from cart successfully', 201);
    }
}
