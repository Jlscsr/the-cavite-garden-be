<?php

namespace Validators;

use Validators\BaseValidator;

use InvalidArgumentException;

class AuthenticationValidator extends BaseValidator
{
    /**
     * Regular expression pattern for validating passwords.
     *
     * The password must meet the following criteria:
     * - At least one uppercase letter
     * - At least one lowercase letter
     * - At least one digit
     * - At least one special character from @$!%*?&
     * - Minimum length of 8 characters
     *
     * @var string $passwordPattern
     */
    static $passwordPattern = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%_*?&]{8,}$/';
    static $phoneNumberPattern = '/^(09|\+639)\d{9}$/';
    static $datePattern = '/^\d{4}-\d{2}-\d{2}$/';
    static $firstNameAndLastNamePattern = '/^[a-zA-Z]+(?:[\-][a-zA-Z]+)*$/';

    /**
     * Validates the register payload.
     *
     * This function validates the register payload by performing the following checks:
     * - Calls the validatePOSTPayload function to validate the payload.
     * - Calls the checkPasswordPattern function to check if the password matches the required pattern.
     * - Checks if all the required keys are present in the payload.
     * - Validates the format of the first name and last name.
     * - Validates the date format of the birthdate.
     * - Validates the phone number format.
     *
     * @param array $payload The register payload to be validated.
     * @throws InvalidArgumentException If any of the required keys are missing in the payload,
     *                                  or if the first name or last name format is invalid,
     *                                  or if the date format is invalid,
     *                                  or if the phone number format is invalid.
     * @return void
     */
    public static function validateRegisterPayload(array $payload): void
    {

        self::validatePOSTPayload($payload);
        self::checkPasswordPattern($payload['password']);

        foreach ($payload as $key => $value) {
            if (!isset($payload[$key])) {
                throw new InvalidArgumentException($key . 'is missing in the payload');
            }
        }

        if (!preg_match(self::$firstNameAndLastNamePattern, $payload['firstName']) || !preg_match(self::$firstNameAndLastNamePattern, $payload['lastName'])) {
            throw new InvalidArgumentException('Invalid first name or last name format');
        }

        if (!preg_match(self::$datePattern, $payload['birthdate'])) {
            throw new InvalidArgumentException('Invalid date format. Date format must be YYYY-MM-DD');
        }


        if (!preg_match(self::$phoneNumberPattern, $payload['phoneNumber'])) {
            throw new InvalidArgumentException('Invalid phone number format');
        }
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

        self::validatePOSTPayload($payload);

        if (!isset($payload['email']) || !isset($payload['password'])) {
            throw new InvalidArgumentException('Email or password is missing in the payload');
        }
    }

    public static function checkPasswordPattern(string $password): void
    {
        if (!preg_match(self::$passwordPattern, $password)) {
            throw new InvalidArgumentException('Password must contain at least one uppercase letter, one lowercase letter, one digit, one special character, and have a minimum length of 8 characters');
        }
    }
}
