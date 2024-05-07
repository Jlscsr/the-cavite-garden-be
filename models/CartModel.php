<?php

namespace Models;

use Helpers\ResponseHelper;

use Models\ProductsModel;

class CartModel
{
    private $pdo;
    private $plant_model;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->plant_model = new ProductsModel($pdo);
    }

    public function getCostumerCartProducts($costumer_id)
    {

        if (!is_integer($costumer_id) || empty($costumer_id)) {
            ResponseHelper::sendErrorResponse("Invalid data or data is empty", 400);
            return;
        }

        $query = "SELECT * FROM cart_tb WHERE customer_id = :customer_id";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':customer_id', $costumer_id, PDO::PARAM_STR);

        try {
            $statement->execute();
            $product_ids = [];
            $products_lists = [];
            $cart_products = $statement->fetchAll(PDO::FETCH_ASSOC);

            foreach ($cart_products as $cart_product) {
                $product_ids[] = $cart_product['product_id'];
            }

            foreach ($product_ids as $product_id) {
                $product = $this->plant_model->getProductByID($product_id);
                $products_lists[] = $product;
            }

            $products = [];
            foreach ($cart_products as $cart_product) {
                $cart_product['product_info'] = $products_lists[array_search($cart_product['product_id'], array_column($products_lists, 'id'))];
                $products[] = $cart_product;
            }
            $cart_products = $products;
            return $cart_products;
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }

    public function addProductToCart($data)
    {

        if (!is_array($data) || empty($data)) {
            ResponseHelper::sendErrorResponse("Invalid data or data is empty", 400);
            return;
        }

        $customer_id = $data['customer_id'];
        $product_id = $data['product_id'];
        $product_quantity = $data['product_quantity'];
        $product_base_price = $data['product_base_price'];

        $get_existing_product_query = "SELECT * FROM cart_tb WHERE customer_id = :customer_id AND product_id = :product_id";

        $statement = $this->pdo->prepare($get_existing_product_query);
        $statement->bindValue(':customer_id', $customer_id, PDO::PARAM_STR);
        $statement->bindValue(':product_id', $product_id, PDO::PARAM_STR);

        try {
            $statement->execute();

            if ($statement->rowCount() > 0) {
                $query = "UPDATE cart_tb SET quantity = quantity + :product_quantity, price = price + (:quantity * :product_base_price) WHERE customer_id = :customer_id AND product_id = :product_id";
                $statement = $this->pdo->prepare($query);
                $statement->bindValue(':customer_id', $customer_id, PDO::PARAM_STR);
                $statement->bindValue(':product_id', $product_id, PDO::PARAM_STR);
                $statement->bindValue(':product_quantity', $product_quantity, PDO::PARAM_INT);
                $statement->bindValue(':quantity', $product_quantity, PDO::PARAM_INT);
                $statement->bindValue(':product_base_price', $product_base_price, PDO::PARAM_INT);

                try {
                    $statement->execute();
                    return $statement->rowCount() > 0;
                } catch (PDOException $e) {
                    ResponseHelper::sendErrorResponse($e->getMessage(), 500);
                    return false;
                }
            } else {
                $total_price = $product_base_price * $product_quantity;
                $query = "INSERT INTO cart_tb (customer_id, product_id, quantity, price) VALUES (:customer_id, :product_id, :product_quantity, :total_price)";
                $statement = $this->pdo->prepare($query);
                $statement->bindValue(':customer_id', $customer_id, PDO::PARAM_STR);
                $statement->bindValue(':product_id', $product_id, PDO::PARAM_STR);
                $statement->bindValue(':product_quantity', $product_quantity, PDO::PARAM_INT);
                $statement->bindValue(':total_price', $total_price, PDO::PARAM_INT);

                try {
                    $statement->execute();
                    return $statement->rowCount() > 0;
                } catch (PDOException $e) {
                    echo "3rd Catch";
                    ResponseHelper::sendErrorResponse($e->getMessage(), 500);
                    return false;
                }
            }
        } catch (PDOException $e) {
            echo "1st Catch";
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
            return false;
        }
    }



    public function deleteProductFromCart($customer_id, $cart_product_id)
    {
        if (!is_integer($customer_id) || empty($customer_id) || !is_integer($cart_product_id) || empty($cart_product_id)) {
            ResponseHelper::sendErrorResponse("Invalid data or data is empty", 400);
            return;
        }

        $query = "DELETE FROM cart_tb WHERE id = :id AND customer_id = :customer_id";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':customer_id', $customer_id, PDO::PARAM_STR);
        $statement->bindValue(':id', $cart_product_id, PDO::PARAM_STR);

        try {
            $statement->execute();
            return $statement->rowCount() === 0;
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
            return false;
        }
    }
}
