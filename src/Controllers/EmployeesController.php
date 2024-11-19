<?php

namespace App\Controllers;

use InvalidArgumentException;
use RuntimeException;

use App\Models\EmployeesModel;

use App\Validators\EmployeesValidator;

use App\Helpers\CookieManager;
use App\Helpers\ResponseHelper;
use App\Helpers\JWTHelper;

class EmployeesController
{
    private $pdo;
    private $jwt;
    private $employeesModel;
    private $cookieManager;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->jwt = new JWTHelper();
        $this->cookieManager = new CookieManager();
        $this->employeesModel = new EmployeesModel($this->pdo);
    }

    /**
     * Retrieves all employees from the database and sends a success response with the data.
     *
     * @throws RuntimeException If there is an error during the retrieval process.
     * @return void
     */
    public function getAllEmployees(): void
    {
        try {
            $employees = $this->employeesModel->getAllEmployees();

            if (!$employees) {
                ResponseHelper::sendErrorResponse('No employees found', 404);
                exit;
            }

            ResponseHelper::sendSuccessResponse($employees, 'Employees retrieved successfully', 200);
        } catch (RuntimeException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Retrieves a specific employee's data based on the authenticated user's JWT token.
     *
     * @throws RuntimeException If an error occurs during the retrieval process.
     * @return void
     */
    public function getEmployeeById(): void
    {
        try {
            $customerID = $this->getCostumerIDFromToken();

            $response = $this->employeesModel->getEmployeeById($customerID);

            if (!$response) {
                ResponseHelper::sendErrorResponse('No Account Found', 404);
                exit;
            }

            ResponseHelper::sendSuccessResponse($response, 'Successfully retrieved account', 200);
        } catch (RuntimeException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Adds a new employee to the database based on the provided payload.
     *
     * @param array $payload The data for the new employee.
     * @throws RuntimeException If there is an error during the addition process.
     * @throws InvalidArgumentException If the payload is invalid.
     * @return void
     */
    public function addNewEmployee(array $payload): void
    {
        try {
            EmployeesValidator::validateAddEmployeeRequest($payload);

            $response = $this->employeesModel->addNewEmployee($payload);

            if (!$response) {
                ResponseHelper::sendErrorResponse('Failed to add new employee', 400);
                exit;
            }

            ResponseHelper::sendSuccessResponse([], 'Employee added successfully', 201);
        } catch (RuntimeException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 500);
        } catch (InvalidArgumentException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Edits an employee based on the provided parameters and payload.
     *
     * @param array $parameter The parameters for editing the employee.
     * @param array $payload The data to update the employee with.
     * @throws RuntimeException If an error occurs during the editing process.
     * @throws InvalidArgumentException If the provided arguments are invalid.
     * @return void
     */
    public function editEmployee(array $parameter, array $payload): void
    {
        try {
            EmployeesValidator::validateEditEmployeeRequest($parameter, $payload);

            $response = $this->employeesModel->editEmployee((int) $parameter['id'], $payload);

            if (!$response) {
                ResponseHelper::sendErrorResponse('Failed to edit employee', 400);
                return;
            }

            ResponseHelper::sendSuccessResponse([], 'Employee edited successfully', 201);
        } catch (RuntimeException $e) {
            throw new RuntimeException($e->getMessage(), 500);
        } catch (InvalidArgumentException $e) {
            ResponseHelper::sendErrorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Retrieves the customer ID from the token.
     *
     * This function retrieves the customer ID from the token by validating the cookie presence,
     * extracting the access token from the cookie header, and decoding the JWT data.
     *
     * @return int The customer ID extracted from the token.
     */
    private function getCostumerIDFromToken(): int
    {
        $cookieHeader = $this->cookieManager->validateCookiePressence();
        $response = $this->cookieManager->extractAccessTokenFromCookieHeader($cookieHeader);
        $decodedToken = (object) $this->jwt->decodeJWTData($response['token']);

        return $decodedToken->id;
    }
}
