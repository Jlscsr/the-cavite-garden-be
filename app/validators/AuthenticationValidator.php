<?php

namespace App\Validators;

use InvalidArgumentException;

use App\Validators\RequestValidator;

class AuthenticationValidator extends RequestValidator
{
    static $registerPayloadRequiredFields = [
        'firstName' => [
            'format' => '/^(?=.{2,50}$)[a-zA-Z]+(?:[\-][a-zA-Z]+)*$/',
            'errorMessage' => 'First name must be 2-50 characters and can contain letters and hyphens.'
        ],
        'lastName' => [
            'format' => '/^(?=.{2,50}$)[a-zA-Z]+(?:[\-][a-zA-Z]+)*$/',
            'errorMessage' => 'Last name must be 2-50 characters and can contain letters and hyphens.'
        ],
        'birthdate' => [
            'format' => '/^\d{4}-\d{2}-\d{2}$/',
            'errorMessage' => 'Birthdate must be in YYYY-MM-DD format.'
        ],
        'phoneNumber' => [
            'format' => '/^(09|\+639)\d{9}$/',
            'errorMessage' => 'Phone number must start with 09 or +639 and be followed by 9 digits.'
        ],
        'customerEmail' => [
            'format' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            'errorMessage' => 'Please enter a valid email address.'
        ],
        'password' => [
            'format' => '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%_*?&]{8,}$/',
            'errorMessage' => 'Password must be at least 8 characters long, include an uppercase letter, a number, and a special character.'
        ]
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
        self::checkRequiredFields($payload, self::$registerPayloadRequiredFields);
        self::checkFieldsPattern($payload, self::$registerPayloadRequiredFields);
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
        }
    }
}
