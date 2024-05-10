<?php

namespace Models;

use Helpers\ResponseHelper;
use Models\EmployeesModel;
use Models\HelperModel;

use PDO;
use RuntimeException;

class CustomersModel
{
    private $pdo;
    private $employee_model;
    private $helper_model;

    // Constants
    private const CUSTOMERS_TABLE = 'customers_tb';
    private const CUSTOMER_ADDRESS_TABLE = 'customer_address_tb';

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
        updated_at FROM " . self::CUSTOMERS_TABLE;

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

        $query = "SELECT * FROM " . self::CUSTOMER_ADDRESS_TABLE . " WHERE customer_id = :customer_id";
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

    public function getCustomerByEmail($customerEmail)
    {
        $query = "SELECT * FROM " . self::CUSTOMERS_TABLE . " WHERE customerEmail = :customerEmail LIMIT 1";
        $statement = $this->pdo->prepare($query);

        $statement->bindValue(':customerEmail', $customerEmail, PDO::PARAM_STR);

        try {
            $statement->execute();

            return $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    public function getCustomerById($customer_id)
    {
        if (!is_integer($customer_id)) {
            return [];
        }

        $query = "SELECT id, first_name, last_name, phone_number, birthdate, email FROM " . self::CUSTOMERS_TABLE . " WHERE id = :customer_id";
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

    public function addNewCustomer($payload)
    {
        $response = $this->helper_model->checkForDuplicateEmail(self::CUSTOMERS_TABLE, $payload['customerEmail']);

        if ($response) {
            throw new RuntimeException('Email already exists');
        }

        $firstName = $payload['firstName'];
        $lastName = $payload['lastName'];
        $birthdate = $payload['birthdate'];
        $phoneNumber = $payload['phoneNumber'];
        $customerEmail = $payload['customerEmail'];
        $password = $payload['password'];

        $query = "INSERT INTO " . self::CUSTOMERS_TABLE . " (firstName, lastName, phoneNumber, birthdate, customerEmail, password) VALUES (:firstName, :lastName, :phoneNumber, :birthdate, :customerEmail, :password)";

        $statement = $this->pdo->prepare($query);

        $bind_params = [
            ':firstName' => $firstName,
            ':lastName' => $lastName,
            ':birthdate' => $birthdate,
            ':customerEmail' => $customerEmail,
            ':phoneNumber' => $phoneNumber,
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

        $query = "INSERT INTO " . self::CUSTOMER_ADDRESS_TABLE . " (customer_id, label , region, province, municipality, barangay, street_blk_lot, landmark) VALUES (:customer_id, :label, :region, :province, :city, :barangay, :street_blk_lot, :landmark)";
        $statement = $this->pdo->prepare($query);

        $bind_params = [
            ':customer_id' => $customer_id,
            ':label' => $address_label,
            ':region' => $region,
            ':province' => $province,
            ':city' => $city,
            ':barangay' => $barangay,
            ':street_blk_lot' => $street_blk_lot,
            ':landmark' => $land_mark,
        ];

        foreach ($bind_params as $param => $value) {
            $statement->bindValue($param, $value, PDO::PARAM_STR);
        }

        try {
            $statement->execute();
            return $statement->rowCount() > 0;
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }
}
