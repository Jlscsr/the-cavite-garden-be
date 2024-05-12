<?php

namespace App\Models;

use PDO;
use PDOException;

use RuntimeException;

use App\Models\HelperModel;

use App\Helpers\ResponseHelper;

class EmployeesModel
{
    private $pdo;
    private $helper_model;

    private const EMPLOYEES_TABLE = 'employees_tb';

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->helper_model = new HelperModel($pdo);
    }

    /**
     * Retrieves all employees from the database.
     *
     * @return array An array of associative arrays representing each employee, with the following keys:
     *               - id: the employee's ID
     *               - firstName: the employee's first name
     *               - lastName: the employee's last name
     *               - middleName: the employee's middle name
     *               - nickname: the employee's nickname
     *               - email: the employee's email address
     *               - birthdate: the employee's birthdate
     *               - sex: the employee's sex
     *               - maritalStatus: the employee's marital status
     *               - role: the employee's role
     *               - status: the employee's status
     *               - dateStarted: the date the employee started working
     *               - createdAt: the date the employee was created
     *               - modifiedAt: the date the employee was last modified
     * @throws RuntimeException If there is an error executing the database query
     */
    public function getAllEmployees(): array
    {
        $query = "
            SELECT 
                id,
                firstName,
                lastName,
                middleName,
                nickname,
                email,
                birthdate,
                sex,
                maritalStatus,
                role,
                status,
                dateStarted,
                createdAt,
                modifiedAt 
            FROM " . self::EMPLOYEES_TABLE;

        $statement = $this->pdo->prepare($query);

        try {
            $statement->execute();

            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage(), 500);
        }
    }

    /**
     * Retrieves an employee from the database by their ID.
     *
     * @param int $employeeID The ID of the employee to retrieve.
     * @return array An associative array representing the employee, with the following keys:
     *               - id: The employee's ID.
     *               - firstName: The employee's first name.
     *               - lastName: The employee's last name.
     *               - middleName: The employee's middle name.
     *               - nickname: The employee's nickname.
     *               - birthdate: The employee's birthdate.
     *               - email: The employee's email address.
     *               - sex: The employee's sex.
     *               - maritalStatus: The employee's marital status.
     *               - status: The employee's status.
     *               - role: The employee's role.
     *               - dateStarted: The date the employee started working.
     *               - createdAt: The date the employee was created.
     *               - modifiedAt: The date the employee was last modified.
     * @throws RuntimeException If there is an error executing the database query.
     */
    public function getEmployeeById(int $employeeID): array
    {
        $query = "
            SELECT 
                id, 
                firstName, 
                lastName, 
                middleName, 
                nickname, 
                birthdate, 
                email, 
                sex, 
                maritalStatus, 
                status, 
                role, 
                dateStarted, 
                createdAt, 
                modifiedAt 
            FROM " . self::EMPLOYEES_TABLE . " WHERE id = :employeeID";

        $statement = $this->pdo->prepare($query);

        $statement->bindValue(':employeeID', $employeeID, PDO::PARAM_INT);

        try {
            $statement->execute();

            return $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage(), 500);
        }
    }

    /**
     * Retrieves an employee from the database by their email address.
     *
     * @param string $customerEmail The email address of the employee.
     * @throws PDOException If there is an error executing the database query.
     * @return array An associative array representing the employee, with the following keys:
     *               - id: The employee's ID.
     *               - firstName: The employee's first name.
     *               - lastName: The employee's last name.
     *               - middleName: The employee's middle name.
     *               - nickname: The employee's nickname.
     *               - birthdate: The employee's birthdate.
     *               - email: The employee's email address.
     *               - sex: The employee's sex.
     *               - maritalStatus: The employee's marital status.
     *               - status: The employee's status.
     *               - role: The employee's role.
     *               - dateStarted: The date the employee started working.
     *               - createdAt: The date the employee was created.
     *               - modifiedAt: The date the employee was last modified.
     */
    public function getEmployeeByEmail(string $customerEmail): array
    {

        $query = "SELECT * FROM " . self::EMPLOYEES_TABLE . " WHERE email = :customerEmail LIMIT 1";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':customerEmail', $customerEmail, PDO::PARAM_STR);

        try {
            $statement->execute();

            return $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Adds a new employee to the database if the email is unique.
     *
     * @param array $payload An array containing employee details.
     *                      - firstName: The first name of the employee.
     *                      - middleName: The middle name of the employee.
     *                      - lastName: The last name of the employee.
     *                      - nickname: The nickname of the employee.
     *                      - birthdate: The birthdate of the employee.
     *                      - sex: The gender of the employee.
     *                      - maritalStatus: The marital status of the employee.
     *                      - employeeEmail: The email address of the employee.
     *                      - password: The password of the employee.
     *                      - role: The role of the employee.
     *                      - dateStarted: The date the employee started.
     * @throws RuntimeException If the email already exists in the database.
     * @return bool Returns true if the employee is successfully added, false otherwise.
     */
    public function addNewEmployee(array $payload): bool
    {
        $response = $this->helper_model->checkForDuplicateEmail('employees_tb', $payload['employeeEmail']);

        if ($response) {
            throw new RuntimeException('Email already exists', 400);
        }

        $firstName = $payload['firstName'];
        $middleName = $payload['middleName'];
        $lastName = $payload['lastName'];
        $nickname = $payload['nickname'];
        $birthdate = $payload['birthdate'];
        $sex = $payload['sex'];
        $maritalStatus = $payload['maritalStatus'];
        $email = $payload['employeeEmail'];
        $password = $payload['password'];
        $role = $payload['role'];
        $status = 'active';
        $dateStarted = $payload['dateStarted'];

        $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 15]);
        $password = $hashed_password;

        $query = "INSERT INTO " . self::EMPLOYEES_TABLE . " (firstName, middleName, lastName, nickname, birthdate, sex,maritalStatus, email, password, role, status, dateStarted) VALUES (:firstName, :middleName, :lastName, :nickname, :birthdate, :sex, :maritalStatus, :email, :password, :role, :status, :dateStarted)";

        $statement = $this->pdo->prepare($query);

        $bind_params = [
            ':firstName' => $lastName,
            ':middleName' => $firstName,
            ':lastName' => $middleName,
            ':nickname' => $nickname,
            ':birthdate' => $birthdate,
            ':sex' => $sex,
            ':maritalStatus' => $maritalStatus,
            ':email' => $email,
            ':password' => $password,
            ':role' => $role,
            ':status' => $status,
            ':dateStarted' => $dateStarted
        ];

        foreach ($bind_params as $key => $value) {
            $statement->bindValue($key, $value, PDO::PARAM_STR);
        }

        try {
            $statement->execute();

            return $statement->rowCount() > 0;
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage(), 500);
        }
    }

    /**
     * Updates an employee's information in the database.
     *
     * @param int $employeeID The ID of the employee to update.
     * @param array $payload An associative array containing the employee's updated information.
     *                      - firstName: The employee's first name.
     *                      - middleName: The employee's middle name.
     *                      - lastName: The employee's last name.
     *                      - nickname: The employee's nickname.
     *                      - birthdate: The employee's birthdate.
     *                      - sex: The employee's gender.
     *                      - maritalStatus: The employee's marital status.
     *                      - role: The employee's role.
     *                      - dateStarted: The date the employee started.
     * @throws RuntimeException If there is an error executing the database query.
     * @return bool Returns true if the employee's information was successfully updated, false otherwise.
     */
    public function editEmployee(int $employeeID, array $payload): bool
    {
        $firstName = $payload['firstName'];
        $middleName = $payload['middleName'];
        $lastName = $payload['lastName'];
        $nickname = $payload['nickname'];
        $birthdate = $payload['birthdate'];
        $sex = $payload['sex'];
        $maritalStatus = $payload['maritalStatus'];
        $role = $payload['role'];
        $dateStarted = $payload['dateStarted'];

        $query = "UPDATE " . self::EMPLOYEES_TABLE . " SET firstName = :firstName, middleName = :middleName, lastName = :lastName, nickname = :nickname, birthdate = :birthdate, sex = :sex, maritalStatus = :maritalStatus, role = :role, dateStarted = :dateStarted WHERE id = :employeeID";

        $statement = $this->pdo->prepare($query);

        $bind_params = [
            ':firstName' => $firstName,
            ':middleName' => $middleName,
            ':lastName' => $lastName,
            ':nickname' => $nickname,
            ':birthdate' => $birthdate,
            ':maritalStatus' => $maritalStatus,
            ':sex' => $sex,
            ':role' => $role,
            ':dateStarted' => $dateStarted,
        ];

        $statement->bindValue(':employeeID', $employeeID, PDO::PARAM_INT);

        foreach ($bind_params as $key => $value) {
            $statement->bindValue($key, $value, PDO::PARAM_STR);
        }

        try {
            $statement->execute();

            return $statement->rowCount() > 0;
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage(), 500);
        }
    }
}
