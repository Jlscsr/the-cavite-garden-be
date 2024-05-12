<?php

namespace App\Models;

use PDO;

use RuntimeException;

use App\Models\HelperModel;

class CustomersModel
{
    private $pdo;
    private $helperModel;

    // Constants
    private const CUSTOMERS_TABLE = 'customers_tb';
    private const CUSTOMER_ADDRESS_TABLE = 'customers_shipping_address_tb';

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->helperModel = new HelperModel($pdo);
    }

    /**
     * Retrieves all customers from the database.
     *
     * This function executes a SQL query to fetch all customers from the database.
     * It retrieves the customer ID, first name, last name, phone number, email,
     * creation date, and update date for each customer. If no customers are found,
     * it throws a RuntimeException with the message "No customers found".
     *
     * Additionally, for each customer, it calls the `getCustomerAddressById` method
     * to retrieve the shipping address and adds it to the customer array.
     *
     * @return array An array of customer data, each containing the customer ID,
     *               first name, last name, phone number, email, creation date,
     *               update date, and shipping address.
     * @throws RuntimeException If an error occurs while fetching customers.
     */
    public function getAllCustomers(): array
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
                throw new RuntimeException('No customers found');
            }

            foreach ($customers as $key => $value) {
                $response = $this->getCustomerAddressById($value['id']);

                $customers[$key]['shipping_address'] = $response;
            }

            return $customers;
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    /**
     * Retrieves a customer from the database by their email address.
     *
     * @param string $customerEmail The email address of the customer.
     * @throws RuntimeException If an error occurs while fetching the customer.
     * @return array An associative array representing the customer data.
     */
    public function getCustomerByEmail(string $customerEmail): array
    {
        $query = "SELECT * FROM " . self::CUSTOMERS_TABLE . " WHERE email = :customerEmail LIMIT 1";
        $statement = $this->pdo->prepare($query);

        $statement->bindValue(':customerEmail', $customerEmail, PDO::PARAM_STR);

        try {
            $statement->execute();

            return $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    /**
     * Retrieves a customer from the database by their ID.
     *
     * @param int $customerID The ID of the customer.
     * @throws RuntimeException If an error occurs while fetching the customer.
     * @return array An associative array representing the customer data, including the ID, first name, last name, phone number, birthdate, and email.
     */
    public function getCustomerById(int $customerID): array
    {
        $query = "SELECT id, firstName, lastName, phoneNumber, birthdate, email FROM " . self::CUSTOMERS_TABLE . " WHERE id = :customerID";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':customerID', $customerID, PDO::PARAM_STR);

        try {
            $statement->execute();
            $customer = $statement->fetch(PDO::FETCH_ASSOC);

            $customerID = (int) $customer['id'];

            $shippingAddress = $this->getCustomerAddressByCustomerId($customerID);

            $customer['shippingAddresses'] = $shippingAddress || [];

            return $customer;
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    /**
     * Retrieves the customer address by customer ID.
     *
     * @param int $customerID The ID of the customer.
     * @throws RuntimeException If an error occurs while fetching the customer address.
     * @return array An associative array representing the customer address data.
     */
    public function getCustomerAddressByCustomerId(int $customerID): array
    {
        $query = "SELECT * FROM " . self::CUSTOMER_ADDRESS_TABLE . " WHERE customerID = :customerID";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':customerID', $customerID, PDO::PARAM_INT);

        try {
            $statement->execute();

            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    /**
     * Adds a new customer to the database.
     *
     * @param array $payload The customer data containing the following keys:
     *                      - firstName: string, the first name of the customer
     *                      - lastName: string, the last name of the customer
     *                      - birthdate: string, the birthdate of the customer in the format 'YYYY-MM-DD'
     *                      - phoneNumber: string, the phone number of the customer
     *                      - customerEmail: string, the email of the customer
     *                      - password: string, the password of the customer
     * @throws RuntimeException If the email already exists in the database
     * @return bool True if the customer was successfully added, false otherwise
     */
    public function addNewCustomer(array $payload): bool
    {
        $response = $this->helperModel->checkForDuplicateEmail(self::CUSTOMERS_TABLE, $payload['customerEmail']);

        if ($response) {
            throw new RuntimeException('Email already exists');
        }

        $firstName = $payload['firstName'];
        $lastName = $payload['lastName'];
        $birthdate = $payload['birthdate'];
        $phoneNumber = $payload['phoneNumber'];
        $customerEmail = $payload['customerEmail'];
        $password = $payload['password'];

        $query = "INSERT INTO " . self::CUSTOMERS_TABLE . " (firstName, lastName, phoneNumber, birthdate, email, password) VALUES (:firstName, :lastName, :phoneNumber, :birthdate, :customerEmail, :password)";

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
            throw new RuntimeException($e->getMessage());
        }
    }

    /**
     * Adds a new user address to the database.
     *
     * @param int $customerID The ID of the customer.
     * @param array $payload The address data containing the following keys:
     *                      - addressLabel: string, the label of the address
     *                      - region: string, the region of the address
     *                      - province: string, the province of the address
     *                      - city: string, the city of the address
     *                      - barangay: string, the barangay of the address
     *                      - streetBlkLt: string, the street, block, lot, and house number of the address
     *                      - landmark: string, the landmark of the address
     * @throws RuntimeException If there is an error executing the query
     * @return bool True if the address was successfully added, false otherwise
     */
    public function addNewUserAddress(int $customerID, array $payload): bool
    {
        $addressLabel = $payload['addressLabel'];
        $region = $payload['region'];
        $province = $payload['province'];
        $city = $payload['city'];
        $barangay = $payload['barangay'];
        $streetBlkLt = $payload['streetBlkLt'];
        $landmark = $payload['landmark'];

        $query = "INSERT INTO " . self::CUSTOMER_ADDRESS_TABLE . " (customerID, addressLabel , region, province, municipality, barangay, streetBlkLt, landmark) VALUES (:customerID, :addressLabel, :region, :province, :city, :barangay, :streetBlkLt, :landmark)";
        $statement = $this->pdo->prepare($query);

        $bind_params = [
            ':addressLabel' => $addressLabel,
            ':region' => $region,
            ':province' => $province,
            ':city' => $city,
            ':barangay' => $barangay,
            ':streetBlkLt' => $streetBlkLt,
            ':landmark' => $landmark,
        ];

        $statement->bindValue(':customerID', $customerID, PDO::PARAM_INT);
        foreach ($bind_params as $param => $value) {
            $statement->bindValue($param, $value, PDO::PARAM_STR);
        }

        try {
            $statement->execute();
            return $statement->rowCount() > 0;
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }
}
