<?php

namespace Validators\Authentication;

class LoginValidator
{
    public static function validatePayload($payload)
    {

        $requiredFields = ['email', 'password'];

        foreach ($payload as $key => $value) {
            if (!in_array($key, $requiredFields)) {
                return $key . ' is not a valid field. ';
            }
        }

        if (!is_array($payload) || empty($payload)) {
            return 'Invalid payload data type or payload is empty';
        }

        if (!isset($payload['email']) || !isset($payload['password'])) {
            return 'Email or password is missing in the payload';
        }

        if (!filter_var($payload['email'], FILTER_VALIDATE_EMAIL)) {
            return 'Invalid email format';
        }

        return 'valid';
    }
}
