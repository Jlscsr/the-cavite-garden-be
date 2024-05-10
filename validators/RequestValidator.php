<?php

namespace Validators;

use Interfaces\ValidatorInterface;

use InvalidArgumentException;

class RequestValidator implements ValidatorInterface
{

    /**
     * Validates the given GET request parameter.
     *
     * @param array $parameter The GET request parameter to validate.
     * @throws InvalidArgumentException If the parameter is empty or not an array.
     * @return void
     */
    public static function validateGETRequest(array $parameter): void
    {
        self::validateArrayNotEmpty($parameter, 'Invalid parameter data type or parameter is empty');
    }

    /**
     * Validates the given POST request payload.
     *
     * This function checks if the given payload is an array and not empty. If it is not,
     * an InvalidArgumentException is thrown with the message 'Payload data type must be an array or payload is empty'.
     *
     * @param array $payload The POST request payload to validate.
     * @throws InvalidArgumentException If the payload is not an array or is empty.
     * @return void
     */
    public static function validatePOSTRequest(array $payload): void
    {
        self::validateArrayNotEmpty($payload, 'Payload data type must be an array or payload is empty');
    }

    /**
     * Validates the given PUT request parameter and payload.
     *
     * @param array $parameter The PUT request parameter to validate.
     * @param array $payload The PUT request payload to validate.
     * @throws InvalidArgumentException If the parameter or payload is empty or not an array.
     * @return void
     */
    public static function validatePUTRequest(array $parameter, array $payload): void
    {
        self::validateArrayNotEmpty($parameter, 'Invalid parameter data type or parameter is empty');
        self::validateArrayNotEmpty($payload, 'Payload data type must be an array or payload is empty');
    }

    /**
     * Validates the given DELETE request parameter.
     *
     * This function checks if the given parameter is an array and not empty. If it is not,
     * an InvalidArgumentException is thrown with the message 'Invalid parameter data type or parameter is empty'.
     *
     * @param array $parameter The DELETE request parameter to validate.
     * @throws InvalidArgumentException If the parameter is empty or not an array.
     * @return void
     */
    public static function validateDELETERequest(array $parameter): void
    {
        self::validateArrayNotEmpty($parameter, 'Invalid parameter data type or parameter is empty');
    }

    /**
     * Validates if an array is not empty.
     *
     * @param array $data The array to validate.
     * @param string $errorMessage The error message to throw if the array is empty.
     * @throws InvalidArgumentException If the array is empty.
     * @return void
     */
    public static function validateArrayNotEmpty(array $data, string $errorMessage): void
    {
        if (empty($data)) {
            throw new InvalidArgumentException($errorMessage);
        }
    }

    /**
     * Checks if the values in the payload array match the required format for each key.
     *
     * @param array $payload The array of key-value pairs to check.
     * @param array $requiredFields An array of required field names and their corresponding regex patterns.
     * @throws InvalidArgumentException If a value in the payload does not match the required format for its key.
     * @return void
     */
    public static function checkFieldsPattern(array $payload, array $requiredFields): void
    {
        foreach ($payload as $key => $value) {
            if ($requiredFields[$key] !== null && !preg_match($requiredFields[$key], $value)) {
                throw new InvalidArgumentException($key . ' format is invalid.');
            }
        }
    }

    /**
     * Checks if the required fields in the payload array are present.
     *
     * This function iterates over each key-value pair in the payload array and checks if the key is present in the
     * requiredFields array. If a key is not found in the requiredFields array, an InvalidArgumentException is thrown
     * with the message indicating that the key is not a valid field.
     *
     * @param array $payload The array of key-value pairs to check for required fields.
     * @param array $requiredFields An array of required field names.
     * @throws InvalidArgumentException If a key in the payload is not present in the requiredFields array.
     * @return void
     */
    public static function checkRequiredFields(array $payload, array $requiredFields): void
    {
        foreach ($payload as $key => $value) {
            $requiredFieldKeys = array_keys($requiredFields);
            if (!in_array($key, $requiredFieldKeys)) {
                throw new InvalidArgumentException($key . ' is not a valid field.');
            }
        }
    }
}
