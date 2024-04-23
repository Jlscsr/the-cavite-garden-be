<?php

use Helpers\ResponseHelper;
use Models\EmployeesModel;
use Models\HelperModel;

class CustomersModel
{
    private $pdo;
    private $employee_model;
    private $helper_model;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->employee_model = new EmployeesModel($pdo);
        $this->helper_model = new HelperModel($pdo);
    }

    public function getAllCustomers()
    {
        $query = "SELECT 
        id,
        first_name,
        last_name,
        phone_number,
        email,
        created_at,
        updated_at FROM customers_tb";

        $statement = $this->pdo->prepare($query);

        try {
            $statement->execute();
            $customers = $statement->fetchAll(PDO::FETCH_ASSOC);

            if (empty($customers)) {
                return [];
            }

            foreach ($customers as $key => $value) {
                $response = $this->getCustomerAddressById($value['id']);

                $customers[$key]['shipping_address'] = $response;
            }

            return $customers;
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
            exit;
        }
    }

    public function getCustomerAddressById($customer_id)
    {
        if (!is_integer($customer_id)) {
            return [];
        }

        $query = "SELECT * FROM customers_shipping_address_tb WHERE customer_id = :customer_id";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':customer_id', $customer_id, PDO::PARAM_STR);

        try {
            $statement->execute();

            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
            exit;
        }
    }

    public function getAccountByEmail($email)
    {
        if (!is_string($email)) {
            return false;
        }

        $query = "SELECT * FROM customers_tb WHERE email = :email";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':email', $email, PDO::PARAM_STR);

        try {
            $statement->execute();

            if ($statement->rowCount() === 0) {
                $response = $this->employee_model->getEmployeeByEmail($email);
                return $response;
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

    public function getCustomerById($customer_id)
    {
        if (!is_integer($customer_id)) {
            return [];
        }

        $query = "SELECT id, first_name, last_name, phone_number, birthdate, email FROM customers_tb WHERE id = :customer_id";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':customer_id', $customer_id, PDO::PARAM_STR);

        try {
            $statement->execute();
            $user_info = $statement->fetchAll(PDO::FETCH_ASSOC);

            $user_shipping_address_id = $user_info[0]['id'];

            $shipping_address = $this->getCustomerAddressById($user_shipping_address_id);

            $user_info[0]['shipping_addresses'] = $shipping_address;

            return $user_info;
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }

    public function addNewCustomer($customerData)
    {
        if (!is_array($customerData) && empty($customerData)) {
            return [];
        }

        $response = $this->helper_model->checkForDuplicateEmail('customers_tb', $customerData['email']);

        if ($response) {
            return "Email already in use";
        }

        $first_name = $customerData['first_name'];
        $last_name = $customerData['last_name'];
        $birthdate = $customerData['birthdate'];
        $phone_number = $customerData['phone_number'];
        $email = $customerData['email'];
        $password = $customerData['password'];

        $query = "INSERT INTO customers_tb (first_name, last_name, phone_number, birthdate, email, password) 
            VALUES (:first_name, :last_name, :phone_number, :birthdate, :email, :password)";

        $statement = $this->pdo->prepare($query);

        $bind_params = [
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':birthdate' => $birthdate,
            ':email' => $email,
            ':phone_number' => $phone_number,
            ':password' => $password,
        ];

        foreach ($bind_params as $param => $value) {
            $statement->bindValue($param, $value, PDO::PARAM_STR);
        }

        try {
            $statement->execute();

            return $statement->rowCount() > 0;
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
            exit;
        }
    }

    public function checkForDuplicateEmail($email)
    {
        if (!is_string($email)) {
            return false;
        }

        $query = "SELECT * FROM customers_tb WHERE email = :email";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':email', $email, PDO::PARAM_STR);

        try {
            $statement->execute();
            return $statement->rowCount() > 0;
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
            exit;
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
}
