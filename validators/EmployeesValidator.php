<?php

namespace Validators;

use Validators\RequestValidator;

use InvalidArgumentException;

class EmployeesValidator extends RequestValidator
{
    static $requiredFields = [
        'firstName' => '/^[a-zA-Z]+(?:[\-][a-zA-Z]+)*$/',
        'middleName' => '/^[a-zA-Z]+(?:[\-][a-zA-Z]+)*$/',
        'lastName' => '/^[a-zA-Z]+(?:[\-][a-zA-Z]+)*$/',
        'nickname' => '/^[a-zA-Z]+(?:[\-][a-zA-Z]+)*$/',
        'birthdate' => '/^\d{4}-\d{2}-\d{2}$/',
        'sex' => '/^(male|female|other)$/i',
        'maritalStatus' => '/^(single|married|divorced)$/i',
        'employeeEmail' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
        'password' => '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%_*?&]{8,}$/',
        'role' => '/^(admin|employee)$/i',
        'dateStarted' => '/^\d{4}-\d{2}-\d{2}$/',
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
        self::checkFieldsPattern($payload, self::$requiredFields);
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
