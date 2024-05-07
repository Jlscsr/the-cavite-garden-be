<?php

use Helpers\CookieManager;
use Helpers\ResponseHelper;
use Helpers\JWTHelper;
use Helpers\HeaderHelper;

use Models\EmployeesModel;


class EmployeesController
{
    private $pdo;
    private $jwt;
    private $employees_model;
    private $cookie_manager;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->jwt = new JWTHelper();
        $this->cookie_manager = new CookieManager();
        $this->employees_model = new EmployeesModel($this->pdo);

        HeaderHelper::setResponseHeaders();
    }

    public function getAllEmployees()
    {
        $lists_of_employees = $this->employees_model->getAllEmployees();

        if (empty($lists_of_employees)) {
            ResponseHelper::sendErrorResponse('No employees found', 404);
            return;
        }

        ResponseHelper::sendSuccessResponse($lists_of_employees, 'Employees fetched successfully', 200);
    }

    public function getEmployeeById()
    {

        $token = $this->cookie_manager->extractAccessTokenFromCookieHeader();
        $customer_id = $this->jwt->decodeJWTData($token)->id;

        $response = $this->employees_model->getEmployeeById($customer_id);

        if (empty($response)) {
            ResponseHelper::sendDatabaseErrorResponse('Failed to fetch account');
            return;
        }

        ResponseHelper::sendSuccessResponse($response, 'Successfully fetched account');
    }

    public function addNewEmployee($payload)
    {

        if (!is_array($payload) || empty($payload)) {
            ResponseHelper::sendErrorResponse("Invalid payload or payload is empty");
            return;
        }

        $response = $this->employees_model->addNewEmployee($payload);

        if (is_string($response)) {
            ResponseHelper::sendDatabaseErrorResponse($response, 409);
            return;
        }

        if (!$response) {
            ResponseHelper::sendErrorResponse('Failed to add new employee', 500);
            return;
        }

        ResponseHelper::sendSuccessResponse([], 'Employee added successfully', 201);
    }

    public function editEmployee($param, $payload)
    {

        if (!is_array($payload) || empty($payload) || !is_array($param) || empty($param) || !isset($param['id'])) {
            ResponseHelper::sendErrorResponse("Invalid payload and paramter or payload and parameter is empty");
            return;
        }

        $response = $this->employees_model->editEmployee($param['id'], $payload);

        if (is_string($response)) {
            ResponseHelper::sendErrorResponse($response);
            return;
        }

        if (!$response) {
            ResponseHelper::sendErrorResponse('Failed to edit employee');
            return;
        }

        ResponseHelper::sendSuccessResponse([], 'Employee edited successfully', 200);
    }
}
