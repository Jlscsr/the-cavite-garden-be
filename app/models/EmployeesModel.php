<?php

namespace App\Models;

use PDO;
use PDOException;

use RuntimeException;

use App\Helpers\ResponseHelper;

use App\Models\HelperModel;

class EmployeesModel
{
    private $pdo;
    private $helperModel;
    private $roleMap = [
        '0' => 'employee',
        '1' => 'admin'
    ];
    private $genderMap = [
        '0' => 'male',
        '1' => 'female'
    ];

    private $maritalStatusMap = [
        '0' => 'single',
        '1' => 'married',
        '2' => 'divorced',
        '3' => 'widowed',
        '4' => 'separated',
        'single' => 0,
        'married' => 1,
        'divorced' => 2,
        'widowed' => 3,
        'separated' => 4
    ];

    private const EMPLOYEES_TABLE = 'employees_tb';

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->helperModel = new HelperModel($pdo);
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
        $query = "SELECT  * FROM " . self::EMPLOYEES_TABLE;

        $statement = $this->pdo->prepare($query);

        try {
            $statement->execute();

            $employees = $statement->fetchAll(PDO::FETCH_ASSOC);

            if ($employees) {
                foreach ($employees as $key => $customer) {
                    unset($employees[$key]['password']);

                    // Ensure the 'role' exists in the customer array before accessing it
                    if (isset($customer['role'])) {
                        $employees[$key]['role'] = $this->roleMap[$customer['role']] ?? 'unknown';
                    }
                }
            }



            return $employees;
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
    public function getEmployeeById(string $employeeID): array | bool
    {
        $query = "
            SELECT * FROM " . self::EMPLOYEES_TABLE . " WHERE id = :employeeID";

        $statement = $this->pdo->prepare($query);

        $statement->bindValue(':employeeID', $employeeID, PDO::PARAM_INT);

        try {
            $statement->execute();

            $employee = $statement->fetch(PDO::FETCH_ASSOC);

            if ($employee) {
                $employee['role'] = $this->roleMap[$employee['role']];
                $employee['gender'] = (string) $this->genderMap[$employee['gender']];
                $employee['maritalStatus'] = (string) $this->maritalStatusMap[$employee['maritalStatus']];
                $employee['nickName'] = $employee['nickname'] ?? 'N/A';
            }


            return $employee;
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
    public function getEmployeeByEmail(string $customerEmail): array | bool
    {

        $query = "SELECT * FROM " . self::EMPLOYEES_TABLE . " WHERE email = :customerEmail LIMIT 1";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':customerEmail', $customerEmail, PDO::PARAM_STR);

        try {
            $statement->execute();

            $employee = $statement->fetch(PDO::FETCH_ASSOC);

            if ($employee) {
                $employee['role'] = $this->roleMap[$employee['role']];
            }

            return $employee;
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
        $response = $this->helperModel->checkForDuplicateEmail('employees_tb', $payload['employeeEmail']);

        if ($response) {
            throw new RuntimeException('Email already exists', 400);
        }

        $id = $this->helperModel->generateUuid();
        $firstName = $payload['firstName'];
        $middleName = $payload['middleName'];
        $lastName = $payload['lastName'];
        $nickname = $payload['nickname'];
        $birthdate = $payload['birthdate'];
        $gender = $payload['sex'] === 'male' ? '0' : '1';
        $maritalStatus = $this->maritalStatusMap[$payload['maritalStatus']];
        $email = $payload['employeeEmail'];
        $password = $this->helperModel->hashPassword($payload['password']);
        $role = $payload['role'];
        $dateStarted = $payload['dateStarted'];

        $query = "INSERT INTO " . self::EMPLOYEES_TABLE . " (id, firstName, middleName, lastName, nickname, birthdate, gender, maritalStatus, email, password, role, dateStarted) VALUES (:id, :firstName, :middleName, :lastName, :nickname, :birthdate, :gender, :maritalStatus, :email, :password, :role, :dateStarted)";

        $statement = $this->pdo->prepare($query);

        $bind_params = [
            ':id' => $id,
            ':firstName' => $lastName,
            ':middleName' => $firstName,
            ':lastName' => $middleName,
            ':nickname' => $nickname,
            ':birthdate' => $birthdate,
            ':gender' => $gender,
            ':maritalStatus' => $maritalStatus,
            ':email' => $email,
            ':password' => $password,
            ':role' => $role,
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
    public function editEmployee(string $employeeID, array $payload): bool
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
            ':employeeID' => $employeeID,
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
