<?php

namespace App\Validators;

use App\Validators\RequestValidator;

class CustomersValidator extends RequestValidator
{
    static $requiredFields = [
        'addressLabel' => '/^[a-zA-Z0-9\s-]+$/',
        'region' => '/^[a-zA-Z0-9\s-]+$/',
        'province' => '/^[a-zA-Z\s-]+$/',
        'city' => '/^[a-zA-Z\s-]+$/',
        'barangay' => '/^[a-zA-Z0-9\s-]+$/',
        'streetBlkLt' => '/^[a-zA-Z0-9\s-]+$/',
        'landmark' => '/^[a-zA-Z0-9\s-]+$/',
    ];

    /**
     * Validates the payload for adding a customer address.
     *
     * @param array $payload The payload containing the customer address data.
     * @throws None
     * @return void
     */
    public static function validateAddCustomerAddress(array $payload): void
    {
        self::validatePOSTRequest($payload);
        self::checkRequiredFields($payload, self::$requiredFields);
        self::checkFieldsPattern($payload, self::$requiredFields);
    }
}
