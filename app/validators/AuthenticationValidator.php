<?php

namespace App\Validators;

use InvalidArgumentException;

use App\Validators\RequestValidator;

class AuthenticationValidator extends RequestValidator
{
    static $requiredFields = [
        'firstName' => '/^[a-zA-Z]+(?:[\-][a-zA-Z]+)*$/',
        'lastName' => '/^[a-zA-Z]+(?:[\-][a-zA-Z]+)*$/',
        'birthdate' => '/^\d{4}-\d{2}-\d{2}$/',
        'phoneNumber' => '/^(09|\+639)\d{9}$/',
        'customerEmail' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
        'password' => '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%_*?&]{8,}$/',
    ];


    /**
     * Validates the register payload.
     *
     * This function validates the register payload by performing the following checks:
     * - Calls the validatePOSTPayload function to validate the payload.
     * - Checks if all the keys in the payload are valid fields.
     * - Checks if the values of the payload match the required formats.
     *
     * @param array $payload The register payload to be validated.
     * @throws InvalidArgumentException If a key is not a valid field or a value does not match the required format.
     * @return void
     */
    public static function validateRegisterPayload(array $payload): void
    {
        self::validatePOSTRequest($payload);
        self::checkRequiredFields($payload, self::$requiredFields);
        // self::checkFieldsPattern($payload, self::$requiredFields);
    }

    /**
     * Validates the login payload.
     *
     * This function validates the login payload by performing the following checks:
     * - Calls the validatePOSTPayload function to validate the payload.
     * - Calls the checkPasswordPattern function to check if the password matches the required pattern.
     * - Checks if the 'email' and 'password' keys are set in the payload.
     *
     * @param array $payload The login payload to be validated.
     * @throws InvalidArgumentException If the 'email' or 'password' is missing in the payload.
     * @return void
     */
    public static function validateLoginPayload(array $payload): void
    {
        self::validatePOSTRequest($payload);

        if (!isset($payload['email']) || !isset($payload['password'])) {
            throw new InvalidArgumentException('Email or password is missing in the payload');
            exit;
        }
    }
}
