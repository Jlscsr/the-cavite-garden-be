<?php
require_once dirname(__DIR__) . '/helpers/ResponseHelper.php';
require_once dirname(__DIR__) . '/model/PlantModel.php';


class TransactionModel
{
    private $pdo;
    private $plant_model;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->plant_model = new PlantModel($pdo);
    }

    public function getAllTransactions($status)
    {
        if (empty($status)) {
            ResponseHelper::sendErrorResponse("Invalid data or data is empty", 400);
            return;
        }

        $success_status = null;
        $failed_status = null;
        $pending_status = null;
        $query = null;

        if (is_array($status)) {
            $success_status = $status[0];
            $failed_status = $status[1];

            $query = "SELECT * FROM transaction_tb WHERE status = :success_status OR status = :failed_status";
        } else {
            $pending_status = $status;
            $query = "SELECT * FROM transaction_tb WHERE status = :pending_status";
        }

        $statement = $this->pdo->prepare($query);

        if ($success_status !== null && $failed_status !== null) {
            $statement->bindValue(':success_status', $success_status, PDO::PARAM_STR);
            $statement->bindValue(':failed_status', $failed_status, PDO::PARAM_STR);
        } else {
            $statement->bindValue(':pending_status', $pending_status, PDO::PARAM_STR);
        }

        try {
            $statement->execute();
            $transactions = $statement->fetchAll(PDO::FETCH_ASSOC);
            $transaction_data = [];

            foreach ($transactions as $key => $transaction_value) {
                $costumer_id = $transaction_value['costumer_id'];
                $transaction_id = $transaction_value['id'];

                $query = "SELECT id, first_name, last_name FROM customer_tb WHERE id = :id";
                $statement = $this->pdo->prepare($query);
                $statement->bindValue(':id', $costumer_id, PDO::PARAM_STR);

                try {
                    $statement->execute();
                    $costumer = $statement->fetch(PDO::FETCH_ASSOC);

                    if ($statement->rowCount() > 0) {

                        $query = "SELECT * FROM product_transaction_tb WHERE transaction_id = :transaction_id";
                        $statement = $this->pdo->prepare($query);
                        $statement->bindValue(':transaction_id', $transaction_id, PDO::PARAM_STR);

                        try {
                            $statement->execute();
                            $product_transaction = $statement->fetchAll(PDO::FETCH_ASSOC);
                            $products = [];

                            foreach ($product_transaction as $key => $value) {
                                $product_id = $value['product_id'];
                                $response = $this->plant_model->getPlantById($product_id);

                                if (!$response) {
                                    ResponseHelper::sendErrorResponse("Product not found", 404);
                                    return;
                                }

                                if (!$response['size']) {
                                    unset($response['size']);
                                }
                                unset($response['stock']);

                                $response['purchased_quantity'] = $value['quantity'];
                                $products[] = $response;
                            }
                            $transaction = [
                                "id" => $transaction_value['id'],
                                "costumer_id" => $transaction_value['costumer_id'],
                                "costumer" => "$costumer[first_name] $costumer[last_name]",
                                "total_price" => $transaction_value['total_price'],
                                "status" => $transaction_value['status'],
                                "transaction_date" => $transaction_value['created_at'],
                                "delivery_method" => $transaction_value['delivery_method'],
                                "payment_method" => $transaction_value['payment_method'],
                                "shipping_address" => $transaction_value['shipping_address'],
                                "products_purchased" => $products
                            ];
                            $transaction_data[] = $transaction;
                        } catch (PDOException $e) {
                            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
                        }
                    }
                } catch (PDOException $e) {
                    ResponseHelper::sendErrorResponse($e->getMessage(), 500);
                }
            }
            return $transaction_data;
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }

    public function getAllTransactionsByCustomerId()
    {
        //
    }

    public function addNewTransaction($costumer_id, $data)
    {
        if (!is_integer($costumer_id) || empty($costumer_id) || !is_array($data) || empty($data)) {
            ResponseHelper::sendErrorResponse("Invalid data or data is empty", 400);
            return;
        }

        $product_total_price = 0;
        $delivery_method = $data['delivery_method'];
        $payment_method = $data['payment_method'];
        $shipping_address = $data['shipping_address'];
        $status = "pending";

        foreach ($data as $key => $value) {
            if (is_array($value) && !empty($value)) {
                $product_total_price += $value['price'];
            }
        }

        $query = "INSERT INTO transaction_tb (costumer_id, total_price, delivery_method, payment_method, shipping_address, status) VALUES (:costumer_id, :product_total_price, :delivery_method, :payment_method, :shipping_address, :status)";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':costumer_id', $costumer_id, PDO::PARAM_STR);
        $statement->bindValue(':product_total_price', $product_total_price, PDO::PARAM_STR);
        $statement->bindValue(':delivery_method', $delivery_method, PDO::PARAM_STR);
        $statement->bindValue(':payment_method', $payment_method, PDO::PARAM_STR);
        $statement->bindValue(':shipping_address', $shipping_address, PDO::PARAM_STR);
        $statement->bindValue(':status', $status, PDO::PARAM_STR);

        $query_status = false;
        try {
            $statement->execute();

            if ($statement->rowCount() > 0) {
                $transaction_id = $this->pdo->lastInsertId();


                foreach ($data as $key => $value) {
                    if (is_array($value) && !empty($value)) {
                        $query = "INSERT INTO product_transaction_tb (transaction_id, product_id, price, quantity) VALUES (:transaction_id, :product_id, :price, :quantity)";
                        $statement = $this->pdo->prepare($query);
                        $statement->bindValue(':transaction_id', $transaction_id, PDO::PARAM_STR);
                        $statement->bindValue(':product_id', $value['product_info']['id'], PDO::PARAM_STR);
                        $statement->bindValue(':price', $value['product_info']['plant_price'], PDO::PARAM_STR);
                        $statement->bindValue(':quantity', $value['quantity'], PDO::PARAM_STR);

                        try {
                            $statement->execute();
                            $query_status = true;
                        } catch (PDOException $e) {
                            $query_status = false;
                            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
                        }
                    }
                }

                if ($query_status) {
                    return $statement->rowCount() > 0;
                }
            }
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }

    public function updateTransactionStatus($id, $status)
    {
        if (!is_string($id) || !is_string($status)) {
            ResponseHelper::sendErrorResponse("Invalid data or data is empty", 400);
            return;
        }

        $query = "UPDATE transaction_tb SET status = :status WHERE id = :id";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_STR);
        $statement->bindValue(':status', $status, PDO::PARAM_STR);

        try {
            $statement->execute();
            return $statement->rowCount() > 0;
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }
}
