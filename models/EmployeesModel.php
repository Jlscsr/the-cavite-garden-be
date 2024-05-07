<?php


namespace Models;

use PDO;
use PDOException;

use Helpers\ResponseHelper;
use Models\HelperModel;

class EmployeesModel
{
    private $pdo;
    private $helper_model;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->helper_model = new HelperModel($pdo);
    }

    public function getAllEmployees()
    {
        $query = "SELECT 
        id,
        first_name,
        last_name,
        middle_name,
        nickname,
        email,
        birthdate,
        sex,
        marital_status,
        role,
        status,
        date_started,
        created_at,
        updated_at FROM employees_tb";

        $statement = $this->pdo->prepare($query);

        try {
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
            exit;
        }
    }

    public function getEmployeeById($employee_id)
    {
        if (!is_integer($employee_id)) {
            return [];
        }

        $query = "SELECT id, first_name, last_name, middle_name, nickname, birthdate, email, sex, marital_status, status, role, date_started, created_at, updated_at FROM employees_tb WHERE id = :employee_id";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':employee_id', $employee_id, PDO::PARAM_STR);

        try {
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }

    public function getEmployeeByEmail($email)
    {

        $query = "SELECT * FROM employees_tb WHERE email = :email LIMIT 1";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':email', $email, PDO::PARAM_STR);

        try {
            $statement->execute();

            return $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }

    public function addNewEmployee($payload)
    {
        if (!is_array($payload) || empty($payload)) {
            return "Invalid payload or payload is empty";
        }

        $is_email_exist = $this->helper_model->checkForDuplicateEmail('employees_tb', $payload['email']);

        if ($is_email_exist) {
            return "Email already exist";
        }

        $first_name = $payload['first_name'];
        $last_name = $payload['last_name'];
        $middle_name = $payload['middle_name'];
        $nickname = $payload['nickname'];
        $birthdate = $payload['birth_date'];
        $sex = $payload['sex'];
        $marital_status = $payload['marital_status'];
        $role = $payload['role'];
        $status = 'active';
        $date_started = $payload['date_started'];
        $email = $payload['email'];
        $password = $payload['password'];

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $password = $hashed_password;

        $query = "INSERT INTO employees_tb (last_name, first_name, middle_name, nickname, birthdate, marital_status, sex, email, password, date_started, role, status) VALUES (:last_name, :first_name, :middle_name, :nickname, :birthdate, :marital_status, :sex, :email, :password, :date_started, :role, :status)";

        $statement = $this->pdo->prepare($query);

        $bind_params = [
            ':last_name' => $last_name,
            ':first_name' => $first_name,
            ':middle_name' => $middle_name,
            ':nickname' => $nickname,
            ':birthdate' => $birthdate,
            ':marital_status' => $marital_status,
            ':sex' => $sex,
            ':email' => $email,
            ':password' => $password,
            ':date_started' => $date_started,
            ':role' => $role,
            ':status' => $status
        ];

        foreach ($bind_params as $key => $value) {
            $statement->bindValue($key, $value, PDO::PARAM_STR);
        }

        try {
            $statement->execute();
            return $statement->rowCount();
        } catch (PDOException $e) {
            ResponseHelper::sendDatabaseErrorResponse($e->getMessage(), 500);
            exit;
        }
    }

    public function editEmployee($employee_id, $payload)
    {
        if (!is_string($employee_id) || empty($employee_id) || !is_array($payload) || empty($payload)) {
            return "Invalid payload or payload is empty";
        }

        $first_name = $payload['first_name'];
        $last_name = $payload['last_name'];
        $middle_name = $payload['middle_name'];
        $nickname = $payload['nickname'];
        $birthdate = $payload['birth_date'];
        $sex = $payload['sex'];
        $marital_status = $payload['marital_status'];
        $role = $payload['role'];
        $date_started = $payload['date_started'];

        $query = "UPDATE employees_tb SET last_name = :last_name, first_name = :first_name, middle_name = :middle_name, nickname = :nickname, birthdate = :birthdate, marital_status = :marital_status, sex = :sex, date_started = :date_started, role = :role WHERE id = :employee_id";

        $statement = $this->pdo->prepare($query);

        $bind_params = [
            ':last_name' => $last_name,
            ':first_name' => $first_name,
            ':middle_name' => $middle_name,
            ':nickname' => $nickname,
            ':birthdate' => $birthdate,
            ':marital_status' => $marital_status,
            ':sex' => $sex,
            ':date_started' => $date_started,
            ':role' => $role,
            'employee_id' => $employee_id
        ];

        foreach ($bind_params as $key => $value) {
            $statement->bindValue($key, $value, PDO::PARAM_STR);
        }

        try {
            $statement->execute();
            return $statement->rowCount();
        } catch (PDOException $e) {
            ResponseHelper::sendDatabaseErrorResponse($e->getMessage(), 500);
            exit;
        }
    }

    public function deleteEmployee()
    {
        //
    }
}
