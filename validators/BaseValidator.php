<?php

namespace Validators;

use Interfaces\ValidatorInterface;

use InvalidArgumentException;

class BaseValidator implements ValidatorInterface
{

    static $requiredFields = ['firstName', 'lastName', 'birthdate', 'phoneNumber', 'email', 'password'];

    public static function validateGETParameter(array $parameter): void
    {
        //
    }

    public static function validatePOSTPayload(array $payload): void
    {

        foreach ($payload as $key => $value) {
            if (!in_array($key, self::$requiredFields)) {
                throw new InvalidArgumentException($key . ' is not a valid field.');
            }
        }

        if (!is_array($payload) || empty($payload)) {
            throw new InvalidArgumentException('Invalid payload data type or payload is empty');
        }

        if (!filter_var($payload['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format');
        }
    }

    public static function validatePUTPayload(array $payload): void
    {
        //
    }

    public static function validateDELETEParameter(array $parameter): void
    {
        //
    }
}
