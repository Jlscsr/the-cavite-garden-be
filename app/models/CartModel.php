<?php

namespace App\Models;

use PDO;
use PDOException;

use RuntimeException;

use App\Models\ProductsModel;

use App\Helpers\ResponseHelper;

class CartModel
{
    private $pdo;
    private $plantModel;

    private const CART_TABLE = "customer_cart_tb";

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->plantModel = new ProductsModel($pdo);
    }

    /**
     * Retrieves the products in the customer's cart.
     *
     * @param int $costumerID The ID of the customer.
     * @return array An array of cart products, each containing the product information.
     * @throws PDOException If there is an error executing the database query.
     */
    public function getCostumerCartProducts(string $costumerID)
    {
        $query = "SELECT * FROM " . self::CART_TABLE . " WHERE customerID = :customerID";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':customerID', $costumerID, PDO::PARAM_STR);

        try {
            $statement->execute();

            $productIDs = [];
            $productLists = [];
            $cartProducts = $statement->fetchAll(PDO::FETCH_ASSOC);

            foreach ($cartProducts as $cartProduct) {
                $productIDs[] = $cartProduct['productID'];
            }

            foreach ($productIDs as $productID) {
                $product = $this->plantModel->getProductByID($productID);
                $productLists[] = $product;
            }

            $products = [];
            foreach ($cartProducts as $cartProduct) {
                $cartProduct['productInfo'] = $productLists[array_search($cartProduct['productID'], array_column($productLists, 'id'))];
                $products[] = $cartProduct;
            }
            $cartProducts = $products;
            return $cartProducts;
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage(), 500);
        }
    }

    public function getProductCartByID(string $id, string $customerID)
    {
        try {
            $query = "SELECT * FROM " . self::CART_TABLE . " WHERE id = :id AND customerID = :customerID";
            $statement = $this->pdo->prepare($query);

            $statement->bindValue(':id', $id, PDO::PARAM_STR);

            $statement->bindValue(':customerID', $customerID, PDO::PARAM_STR);

            $statement->execute();
            $cartProduct = $statement->fetch(PDO::FETCH_ASSOC);

            $productID = $cartProduct['productID'];

            $product = $this->plantModel->getProductByID($productID);

            $cartProduct['productInfo'] = $product;

            return $cartProduct;
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage(), 500);
        }
    }

    /**
     * Adds a product to the customer's cart.
     *
     * @param array $payload An array containing the following keys:
     *                      - customerID: The ID of the customer.
     *                      - productID: The ID of the product.
     *                      - productQuantity: The quantity of the product.
     *                      - productBasePrice: The base price of the product.
     * @throws RuntimeException If there is an error executing the SQL statements.
     * @return bool Returns true if the product was successfully added to the cart, false otherwise.
     */
    public function addProductToCart(array $payload): bool
    {
        $id = $payload['id'];
        $customerID = $payload['customerID'];
        $productID = $payload['productID'];
        $productQuantity = $payload['productQuantity'];
        $productInitialPrice = $payload['productInitialPrice'];
        $totalPrice = $payload['totalPrice'];

        $query = "SELECT * FROM " . self::CART_TABLE . " WHERE customerID = :customerID AND productID = :productID";

        $statement = $this->pdo->prepare($query);

        $statement->bindValue(':customerID', $customerID, PDO::PARAM_STR);
        $statement->bindValue(':productID', $productID, PDO::PARAM_STR);

        try {
            $statement->execute();

            if ($statement->rowCount() > 0) {

                $query = "UPDATE " . self::CART_TABLE . " SET productQuantity = :productQuantity, productInitialPrice = :productInitialPrice, totalPrice = :totalPrice WHERE customerID = :customerID AND productID = :productID";

                $statement = $this->pdo->prepare($query);

                $statement->bindValue(':customerID', $customerID, PDO::PARAM_STR);
                $statement->bindValue(':productID', $productID, PDO::PARAM_STR);
                $statement->bindValue(':productQuantity', $productQuantity, PDO::PARAM_STR);
                $statement->bindValue(':productInitialPrice', $productInitialPrice, PDO::PARAM_STR);
                $statement->bindValue(':totalPrice', $totalPrice, PDO::PARAM_STR);

                $statement->execute();

                return $statement->rowCount() > 0;
            } else {

                $query = "INSERT INTO " . self::CART_TABLE . " (id, customerID, productID, productQuantity, productInitialPrice, totalPrice) VALUES (:id, :customerID, :productID, :productQuantity, :productInitialPrice, :totalPrice)";
                $statement = $this->pdo->prepare($query);

                $statement->bindValue(':id', $id, PDO::PARAM_STR);
                $statement->bindValue(':customerID', $customerID, PDO::PARAM_STR);
                $statement->bindValue(':productID', $productID, PDO::PARAM_STR);
                $statement->bindValue(':productQuantity', $productQuantity, PDO::PARAM_INT);
                $statement->bindValue(':productInitialPrice', $productInitialPrice, PDO::PARAM_INT);
                $statement->bindValue(':totalPrice', $totalPrice, PDO::PARAM_INT);

                $statement->execute();

                return $statement->rowCount() > 0;
            }
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage(), 500);
        }
    }

    /**
     * Deletes a product from the customer's cart.
     *
     * @param int $customerID The ID of the customer.
     * @param int $cartProductID The ID of the cart product.
     * @throws RuntimeException If there is an error executing the SQL statements.
     * @return bool Returns true if the product was successfully deleted, false otherwise.
     */
    public function deleteProductFromCart(int $customerID, int $cartProductID): bool
    {
        $query = "DELETE FROM " . self::CART_TABLE . " WHERE id = :id AND customerID = :customerID";
        $statement = $this->pdo->prepare($query);

        $statement->bindValue(':customerID', $customerID, PDO::PARAM_STR);
        $statement->bindValue(':id', $cartProductID, PDO::PARAM_STR);

        try {
            $statement->execute();

            return $statement->rowCount() > 0;
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage(), 500);
        }
    }
}
