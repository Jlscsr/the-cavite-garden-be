<?php

namespace App\Validators;

use InvalidArgumentException;

use App\Validators\RequestValidator;

class EmployeesValidator extends RequestValidator
{
    static $requiredFields = [
        'firstName' => [
            'format' => '/^[a-zA-Z]+(?:[\-][a-zA-Z]+)*$/',
            'errorMessage' => 'First name must contain only letters and hyphens.',
            'required' => true
        ],
        'middleName' => [
            'format' => '/^[a-zA-Z]+(?:[\-][a-zA-Z]+)*$/',
            'errorMessage' => 'Middle name must contain only letters and hyphens.',
            'required' => true
        ],
        'lastName' => [
            'format' => '/^[a-zA-Z]+(?:[\-][a-zA-Z]+)*$/',
            'errorMessage' => 'Last name must contain only letters and hyphens.',
            'required' => true
        ],
        'nickname' => [
            'format' => '/^[a-zA-Z]+(?:[\-][a-zA-Z]+)*$/',
            'errorMessage' => 'Nickname must contain only letters and hyphens.',
            'required' => true
        ],
        'birthdate' => [
            'format' => '/^\d{4}-\d{2}-\d{2}$/',
            'errorMessage' => 'Birthdate must be in the format YYYY-MM-DD.',
            'required' => true
        ],
        'sex' => [
            'format' => '/^(male|female)$/i',
            'errorMessage' => 'Sex must be either "male" or "female".',
            'required' => true
        ],
        'maritalStatus' => [
            'format' => '/^(single|married|divorced|widowed)$/i',
            'errorMessage' => 'Marital status must be one of "single", "married", "divorced", or "widowed".',
            'required' => true
        ],
        'employeeEmail' => [
            'format' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            'errorMessage' => 'Invalid email format.',
            'required' => true
        ],
        'password' => [
            'format' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/',
            'errorMessage' => 'Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, and one number.',
            'required' => true
        ],
        'role' => [
            'format' => '/^(admin|employee)$/i',
            'errorMessage' => 'Role must be either "admin" or "employee".',
            'required' => true
        ],
        'dateStarted' => [
            'format' => '/^\d{4}-\d{2}-\d{2}$/',
            'errorMessage' => 'Date started must be in the format YYYY-MM-DD.',
            'required' => true
        ],
    ];

    /**
     * Validates the given payload for adding a new employee.
     *
     * This function checks if the payload is a valid POST request by calling the `validatePOSTRequest` method.
     * It then checks if all the required fields in the payload are present by calling the `checkRequiredFields` method.
     * Finally, it checks if the values of the payload match the specified patterns by calling the `checkFieldsPattern` method.
     *
     * @param array $payload The payload containing the data for the new employee.
     * @throws InvalidArgumentException If the payload is invalid.
     * @return void
     */
    public static function validateAddEmployeeRequest(array $payload): void
    {
        self::validatePOSTRequest($payload);
        self::checkRequiredFields($payload, self::$requiredFields);
        // self::checkFieldsPattern($payload, self::$requiredFields);
    }

    /**
     * Validates the given edit employee request.
     *
     * This function validates the given PUT request parameter and payload for editing an employee. 
     * It first calls the `validatePUTRequest` method to validate the parameter and payload. 
     * Then, it checks if all the required fields in the payload are present by calling the `checkRequiredFields` method. 
     * Finally, it checks if the values of the payload match the specified patterns by calling the `checkFieldsPattern` method.
     *
     * @param array $parameter The PUT request parameter to validate.
     * @param array $payload The PUT request payload to validate.
     * @throws InvalidArgumentException If the parameter or payload is empty or not an array, 
     *         or if the required fields in the payload do not match the specified patterns.
     * @return void
     */
    public static function validateEditEmployeeRequest(array $parameter, array $payload): void
    {
        self::validatePUTRequest($parameter, $payload);
        self::checkRequiredFields($payload, self::$requiredFields);
        self::checkFieldsPattern($payload, self::$requiredFields);
    }
}
