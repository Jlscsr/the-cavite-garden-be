<?php
require_once dirname(__DIR__) . '/helpers/JWTHelper.php';
require_once dirname(__DIR__) . '/helpers/HeaderHelper.php';
require_once dirname(__DIR__) . '/helpers/ResponseHelper.php';
require_once dirname(__DIR__) . '/model/CartModel.php';

class CartController
{
    private $pdo;
    private $jwt;
    private $cart_model;


    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->jwt = new JWTHelper();
        $this->cart_model = new CartModel($pdo);
    }

    public function addToCart($data)
    {
        if (!is_array($data) || empty($data)) {
            ResponseHelper::sendErrorResponse("Invalid data or data is empty", 400);
            return;
        }

        HeaderHelper::setHeaders();

        $token = getallheaders()['Cookie'];
        $token = str_replace("tcg_access_token=", "", $token);

        $decoded_token = $this->jwt->decodeData($token);
        $data['customer_id'] = $decoded_token->id;

        $response = $this->cart_model->addProductToCart($data);

        if (!$response) {
            ResponseHelper::sendErrorResponse($response, 500);
            return;
        }

        ResponseHelper::sendSuccessResponse(null, 'Product added to cart successfully', 201);
    }

    public function getCostumerCartProducts()
    {

        $token = getallheaders()['Cookie'];
        $token = str_replace("tcg_access_token=", "", $token);

        $decoded_token = $this->jwt->decodeData($token);
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

        HeaderHelper::setHeaders();

        $token = getallheaders()['Cookie'];
        $token = str_replace("tcg_access_token=", "", $token);
        $customer_id = $this->jwt->decodeData($token)->id;
        $cart_product_id = $param['id'];
        $cart_product_id = (int) $cart_product_id;


        $response = $this->cart_model->deleteProductFromCart($customer_id, $cart_product_id);

        ResponseHelper::sendSuccessResponse(null, 'Product deleted from cart successfully', 201);
    }
}
