<?php

namespace Models;

use Helpers\ResponseHelper;

use Models\ProductsModel;

use PDO;

use RuntimeException;

class CartModel
{
    private $pdo;
    private $plantModel;

    private const CART_TABLE = "cart_tb";

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
    public function getCostumerCartProducts(int $costumerID): array
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
                $product = $this->plantModel->getProductByID((int) $productID);
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
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
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
        $customerID = $payload['customerID'];
        $productID = $payload['productID'];
        $productQuantity = $payload['productQuantity'];
        $productBasePrice = $payload['productBasePrice'];

        $query = "SELECT * FROM " . self::CART_TABLE . " WHERE customerID = :customerID AND productID = :productID";

        $statement = $this->pdo->prepare($query);

        $statement->bindValue(':customerID', $customerID, PDO::PARAM_INT);
        $statement->bindValue(':productID', $productID, PDO::PARAM_INT);

        try {
            $statement->execute();

            if ($statement->rowCount() > 0) {

                $query = "UPDATE " . self::CART_TABLE . " SET productQuantity = productQuantity + :productQuantity, totalPrice = totalPrice + (:quantity * :productBasePrice) WHERE customerID = :customerID AND productID = :productID";
                $statement = $this->pdo->prepare($query);

                $statement->bindValue(':customerID', $customerID, PDO::PARAM_INT);
                $statement->bindValue(':productID', $productID, PDO::PARAM_INT);
                $statement->bindValue(':productQuantity', $productQuantity, PDO::PARAM_INT);
                $statement->bindValue(':quantity', $productQuantity, PDO::PARAM_INT);
                $statement->bindValue(':productBasePrice', $productBasePrice, PDO::PARAM_INT);

                $statement->execute();

                return $statement->rowCount() > 0;
            } else {
                $totalPrice = $productBasePrice * $productQuantity;

                $query = "INSERT INTO " . self::CART_TABLE . " (customerID, productID, productQuantity, totalPrice) VALUES (:customerID, :productID, :productQuantity, :totalPrice)";
                $statement = $this->pdo->prepare($query);

                $statement->bindValue(':customerID', $customerID, PDO::PARAM_STR);
                $statement->bindValue(':productID', $productID, PDO::PARAM_STR);
                $statement->bindValue(':productQuantity', $productQuantity, PDO::PARAM_INT);
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
