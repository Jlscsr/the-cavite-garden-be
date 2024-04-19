<?php

class AccountsModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllCustomers()
    {
        //
    }

    public function getAccountByEmail($email)
    {
        if (!is_string($email)) {
            return [];
        }

        $query = "SELECT * FROM customer_tb WHERE email = :email";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':email', $email, PDO::PARAM_STR);

        try {
            $statement->execute();

            if ($statement->rowCount() === 0) {
                $query = "SELECT * FROM employee_tb WHERE email = :email";
                $statement = $this->pdo->prepare($query);
                $statement->bindValue(':email', $email, PDO::PARAM_STR);

                try {
                    $statement->execute();
                    $account_details = $statement->fetchAll(PDO::FETCH_ASSOC);
                    $data = [
                        'data' => $account_details,
                        'role' => $account_details[0]['role']
                    ];
                    return $data;
                } catch (PDOException $e) {
                    ResponseHelper::sendErrorResponse($e->getMessage(), 500);
                }
            }
            $data = [
                'data' => $statement->fetchAll(PDO::FETCH_ASSOC),
                'role' => 'customer',
            ];
            return $data;
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }

    public function getAccountById($customer_id)
    {
        if (!is_integer($customer_id)) {
            return [];
        }

        $query = "SELECT id, first_name, last_name, phone_number, birthdate, email FROM customer_tb WHERE id = :customer_id";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':customer_id', $customer_id, PDO::PARAM_STR);

        try {
            $statement->execute();
            $user_info = $statement->fetchAll(PDO::FETCH_ASSOC);

            $user_shipping_address_id = $user_info[0]['id'];

            $query = "SELECT * FROM customers_shipping_address_tb WHERE customer_id = :customer_id";
            $statement = $this->pdo->prepare($query);
            $statement->bindValue(':customer_id', $user_shipping_address_id, PDO::PARAM_STR);

            try {
                $statement->execute();
                $shipping_address = $statement->fetchAll(PDO::FETCH_ASSOC);

                $user_info[0]['shipping_addresses'] = $shipping_address;

                return $user_info;
            } catch (\Throwable $th) {
                //throw $th;
            }
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }

    public function addNewAccount($customerData)
    {
        if (!is_array($customerData) && empty($customerData)) {
            return [];
        }

        $first_name = $customerData['first_name'];
        $last_name = $customerData['last_name'];
        $birthdate = $customerData['birthdate'];
        $phone_number = $customerData['phone_number'];
        $email = $customerData['email'];
        $password = $customerData['password'];

        $query = "INSERT INTO customer_tb (first_name, last_name, phone_number, birthdate, email, password) 
            VALUES (:first_name, :last_name, :phone_number, :birthdate, :email, :password)";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':first_name', $first_name, PDO::PARAM_STR);
        $statement->bindValue(':last_name', $last_name, PDO::PARAM_STR);
        $statement->bindValue(':birthdate', $birthdate, PDO::PARAM_STR);
        $statement->bindValue(':email', $email, PDO::PARAM_STR);
        $statement->bindValue(':phone_number', $phone_number, PDO::PARAM_STR);
        $statement->bindValue(':password', $password, PDO::PARAM_STR);

        try {
            $statement->execute();

            return $statement->rowCount() > 0;
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }

    public function addNewUserAddress($customer_id, $data)
    {
        if (!is_integer($customer_id) || empty($customer_id) || !is_array($data) || empty($data)) {
            ResponseHelper::sendErrorResponse("Invalid data or data is empty", 400);
            return;
        }

        $address_label = $data['label'];
        $region = $data['region'];
        $province = $data['province'];
        $city = $data['city'];
        $barangay = $data['barangay'];
        $street_blk_lot = $data['street_blk_lot'];
        $land_mark = $data['landmark'];

        $query = "INSERT INTO customers_shipping_address_tb (customer_id, label , region, province, municipality, barangay, street_blk_lot, landmark) VALUES (:customer_id, :label, :region, :province, :city, :barangay, :street_blk_lot, :landmark)";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':customer_id', $customer_id, PDO::PARAM_STR);
        $statement->bindValue(':label', $address_label, PDO::PARAM_STR);
        $statement->bindValue(':region', $region, PDO::PARAM_STR);
        $statement->bindValue(':province', $province, PDO::PARAM_STR);
        $statement->bindValue(':city', $city, PDO::PARAM_STR);
        $statement->bindValue(':barangay', $barangay, PDO::PARAM_STR);
        $statement->bindValue(':street_blk_lot', $street_blk_lot, PDO::PARAM_STR);
        $statement->bindValue(':landmark', $land_mark, PDO::PARAM_STR);

        try {
            $statement->execute();
            return $statement->rowCount() > 0;
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }

    public function editCustomer()
    {
        //
    }

    public function deleteCustomer()
    {
        //
    }
}
