<?php

namespace App\Models;

use PDO;
use PDOException;

use RuntimeException;

use App\Models\HelperModel;

class CustomersModel
{
    private $pdo;
    private $helperModel;
    private $roleMap = [
        0 => 'customer',
    ];

    // Constants
    private const CUSTOMERS_TABLE = 'customers_tb';
    private const CUSTOMER_ADDRESS_TABLE = 'customers_ship_address_tb';

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->helperModel = new HelperModel($pdo);
    }

    public function getAllCustomers(): array
    {
        $query = "SELECT * FROM " . self::CUSTOMERS_TABLE;

        $statement = $this->pdo->prepare($query);

        try {
            $statement->execute();
            $customers = $statement->fetchAll(PDO::FETCH_ASSOC);

            if (empty($customers)) {
                throw new RuntimeException('No customers found');
            }

            unset($customers['password']);

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
    public function getCustomerByEmail(string $customerEmail): array | bool
    {
        $query = "SELECT * FROM " . self::CUSTOMERS_TABLE . " WHERE email = :customerEmail";
        $statement = $this->pdo->prepare($query);

        $statement->bindValue(':customerEmail', $customerEmail, PDO::PARAM_STR);

        try {
            $statement->execute();

            $customer = $statement->fetch(PDO::FETCH_ASSOC);

            if ($customer) {
                $customer['role'] = $this->roleMap[$customer['role']];
            }
            return $customer;
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
    public function getCustomerById(string $customerID): array | bool
    {
        $query = "SELECT * FROM " . self::CUSTOMERS_TABLE . " WHERE id = :customerID";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':customerID', $customerID, PDO::PARAM_STR);

        try {
            $statement->execute();
            $customer = $statement->fetch(PDO::FETCH_ASSOC);

            if ($customer) {
                unset($customer['password']);
                $customer['role'] = $this->roleMap[$customer['role']];
                $customerID =  $customer['id'];

                $shippingAddress = $this->getCustomerAddressByCustomerId($customerID);

                $customer['shippingAddresses'] = $shippingAddress ? $shippingAddress : [];
            }

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
    public function getCustomerAddressByCustomerId(string $customerID): array
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

        $id = $this->helperModel->generateUuid();
        $password = $this->helperModel->hashPassword($payload['password']);
        $firstName = $payload['firstName'];
        $lastName = $payload['lastName'];
        $birthdate = $payload['birthdate'];
        $phoneNumber = $payload['phoneNumber'];
        $customerEmail = $payload['customerEmail'];

        $query = "INSERT INTO " . self::CUSTOMERS_TABLE . " (id, firstName, lastName, phoneNumber, birthdate, email, password) VALUES (:id, :firstName, :lastName, :phoneNumber, :birthdate, :customerEmail, :password)";

        $statement = $this->pdo->prepare($query);

        $bind_params = [
            ':id' => $id,
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
            $isSuccess = $statement->rowCount() > 0;

            if ($isSuccess) {
                $currentAddress = $payload['currentAddress'];
                $permanentAddress = $payload['permanentAddress'];

                if ($currentAddress === $permanentAddress) {
                    $isAddressAddingSuccess = $this->addNewUserAddress($id, $currentAddress);
                } else {
                    $currentSuccess = $this->addNewUserAddress($id, $currentAddress);
                    $permanentSuccess = $this->addNewUserAddress($id, $permanentAddress);

                    $isAddressAddingSuccess = $currentSuccess && $permanentSuccess;
                }

                return $isAddressAddingSuccess;
            }
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    public function updateUserData($customerID, $payload)
    {
        $firstName = $payload['firstName'];
        $lastName = $payload['lastName'];
        $phoneNumber = $payload['phoneNumber'];
        $birthdate = $payload['birthdate'];

        $query = "UPDATE " . self::CUSTOMERS_TABLE . " SET firstName = :firstName, lastName = :lastName, phoneNumber = :phoneNumber, birthdate = :birthdate WHERE id = :customerID";
        $statement = $this->pdo->prepare($query);

        $bind_params = [
            ':firstName' => $firstName,
            ':lastName' => $lastName,
            ':phoneNumber' => $phoneNumber,
            ':birthdate' => $birthdate,
            ':customerID' => $customerID,
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
    public function addNewUserAddress(string $customerID, array $payload): bool
    {
        $id = $this->helperModel->generateUuid();
        $addressLabel = $payload['addressLabel'];
        $region = $payload['region'];
        $province = $payload['province'];
        $city = $payload['city'];
        $barangay = $payload['barangay'];
        $postalCode = $payload['postalCode'];
        $streetAddress = $payload['streetAddress'];
        $landmark = $payload['landmark'];

        $query = "INSERT INTO " . self::CUSTOMER_ADDRESS_TABLE . " (id, customerID, addressLabel , region, province, municipality, barangay, postalCode, streetAddress, landmark) VALUES (:id, :customerID, :addressLabel, :region, :province, :city, :barangay, :postalCode, :streetAddress, :landmark)";
        $statement = $this->pdo->prepare($query);

        $bind_params = [
            ':id' => $id,
            ':customerID' => $customerID,
            ':addressLabel' => $addressLabel,
            ':region' => $region,
            ':province' => $province,
            ':city' => $city,
            ':barangay' => $barangay,
            ':postalCode' => $postalCode,
            ':streetAddress' => $streetAddress,
            ':landmark' => $landmark,
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

    public function updateCustomerAddress($customerID, $addressID, $payload)
    {
        // Check first there is a change in the address from the payload and the current address data in the database
        // If there is no change, return [success => true, message => 'No changes made']
        // If there is a change, update the address data in the database

        $currentAddress = $this->getCustomerAddressById($customerID);

        if ($currentAddress) {
            $currentAddress = $currentAddress[0];
        }

        $addressLabel = $payload['addressLabel'];
        $region = $payload['region'];
        $province = $payload['province'];
        $city = $payload['city'];
        $barangay = $payload['barangay'];
        $postalCode = $payload['postalCode'];
        $streetAddress = $payload['streetAddress'];
        $landmark = $payload['landmark'];

        if (
            $currentAddress['addressLabel'] === $addressLabel &&
            $currentAddress['region'] === $region &&
            $currentAddress['province'] === $province &&
            $currentAddress['municipality'] === $city &&
            $currentAddress['barangay'] === $barangay &&
            $currentAddress['postalCode'] === $postalCode &&
            $currentAddress['streetAddress'] === $streetAddress &&
            $currentAddress['landmark'] === $landmark
        ) {
            return ['status' => 'success', 'message' => 'No changes made'];
        }

        $query = "UPDATE " . self::CUSTOMER_ADDRESS_TABLE . " SET addressLabel = :addressLabel, region = :region, province = :province, municipality = :city, barangay = :barangay, postalCode = :postalCode, streetAddress = :streetAddress, landmark = :landmark WHERE customerID = :customerID AND id = :addressID";
        $statement = $this->pdo->prepare($query);

        $bind_params = [
            ':addressLabel' => $addressLabel,
            ':region' => $region,
            ':province' => $province,
            ':city' => $city,
            ':barangay' => $barangay,
            ':postalCode' => $postalCode,
            ':streetAddress' => $streetAddress,
            ':landmark' => $landmark,
            ':customerID' => $customerID,
            ':addressID' => $addressID,
        ];

        foreach ($bind_params as $param => $value) {
            $statement->bindValue($param, $value, PDO::PARAM_STR);
        }

        try {
            $statement->execute();

            if ($statement->rowCount() === 0) {
                return ['status' => 'failed', 'message' => 'Failed to update address'];
            }

            return ['status' => 'success', 'message' => 'Address updated successfully'];
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    public function deleteCustomerAddress($customerID, $addressID)
    {
        $query = "DELETE FROM " . self::CUSTOMER_ADDRESS_TABLE . " WHERE customerID = :customerID AND id = :addressID";
        $statement = $this->pdo->prepare($query);

        $statement->bindValue(':customerID', $customerID, PDO::PARAM_STR);
        $statement->bindValue(':addressID', $addressID, PDO::PARAM_STR);

        try {
            $statement->execute();

            return $statement->rowCount() > 0;
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    protected function getCustomerAddressById(string $customerID): array
    {
        $query = "SELECT * FROM " . self::CUSTOMER_ADDRESS_TABLE . " WHERE customerID = :customerID";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':customerID', $customerID, PDO::PARAM_STR);

        try {
            $statement->execute();

            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }
}
