<?php

namespace App\Validators;

use InvalidArgumentException;

use App\Validators\RequestValidator;

class TransactionsValidator extends RequestValidator
{
    static $requiredParameters = ['status', 'customerID', 'orderPurpose'];
    static $requiredFields = [
        'orderType' => [
            'format' => '/^(delivery|pickup)$/i',
            'errorMessage' => 'Order type must be either "delivery" or "pickup".',
            'required' => true,
        ],
        'paymentType' => [
            'format' => '/^(over-the-counter|cod|gcash)$/i',
            'errorMessage' => 'Payment type must be either "over-the-counter" or "gcash".',
            'required' => true
        ],

        'shippingAddress' => [
            'format' => '/^[a-zA-Z0-9\s]{3,}$/',
            'errorMessage' => 'Shipping address must be at least 3 characters long and contain only letters, numbers, and spaces.',
            'required' => false
        ],
        'status' => [
            'format' => null,
            'errorMessage' => '',
            'required' => true
        ],
        'purchasedProducts' => [
            'format' => null,
            'errorMessage' => '',
            'required' => true
        ]
    ];

    /**
     * Validates the GET request parameters for getting transactions by parameter.
     *
     * @param array $parameter The array containing the parameters to be validated.
     * @throws InvalidArgumentException If the parameter is invalid.
     * @return void
     */
    public static function validateGetTransactionsByParameter(array $parameter): void
    {
        self::validateGETRequest($parameter);

        foreach ($parameter as $key => $value) {
            if (!in_array($key, self::$requiredParameters)) {
                throw new InvalidArgumentException($key . ' is not a valid parameter.');
            }
        }
    }

    /**
     * Validates the add transaction request payload.
     *
     * This function validates the given payload for adding a new transaction by performing the following checks:
     * - Calls the validatePOSTRequest function to validate the payload.
     * - Checks if all the required fields in the payload are present.
     * - Checks if the values of the payload match the required formats.
     *
     * @param array $payload The payload containing the data for the new transaction.
     * @throws InvalidArgumentException If the payload is invalid.
     * @return void
     */
    public static function validateAddTransactionRequest(array $payload): void
    {
        self::validatePOSTRequest($payload);
        self::checkRequiredFields($payload, self::$requiredFields);
        // self::checkFieldsPattern($payload, self::$requiredFields);
    }

    /**
     * Validates the edit transaction request.
     *
     * This function validates the given PUT request parameter and payload for editing a transaction. 
     * It first calls the validatePUTRequest method to validate the parameter and payload. 
     * Then, it checks if all the required fields in the payload are present by calling the checkRequiredFields method. 
     * Finally, it checks if the values of the payload match the specified patterns by calling the checkFieldsPattern method.
     *
     * @param array $parameter The PUT request parameter to validate.
     * @param array $payload The PUT request payload to validate.
     * @throws InvalidArgumentException If the parameter or payload is empty or not an array, 
     *         or if the required fields in the payload do not match the specified patterns.
     * @return void
     */
    public static function validateEditTransactionRequest(array $parameter, array $payload): void
    {
        self::validatePUTRequest($parameter, $payload);
        // self::checkRequiredFields($payload, self::$requiredFields);
        self::checkFieldsPattern($payload, self::$requiredFields);
    }
}
