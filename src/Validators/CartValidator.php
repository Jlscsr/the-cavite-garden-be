<?php

namespace App\Validators;

use InvalidArgumentException;

use App\Validators\RequestValidator;

class CartValidator extends RequestValidator
{
    static $requiredParameters = ['id'];
    static $requiredFields = [
        'productID' => '/\b\d{1,11}\b/',
        'productQuantity' => '/\b\d{1,11}\b/',
        'productBasePrice' => '/\b\d{1,8}(\.\d{1,2})?\b/'
    ];


    /**
     * Validates the request to add a product to the cart.
     *
     * @param array $payload The data for the product being added to the cart.
     * @throws InvalidArgumentException If the payload is invalid.
     * @return void
     */
    public static function validateAddProductToCartRequest(array $payload): void
    {
        self::validatePOSTRequest($payload);
        self::checkRequiredFields($payload, self::$requiredFields);
        self::checkFieldsPattern($payload, self::$requiredFields);
    }

    /**
     * Validates the request to delete a product from the cart.
     *
     * @param array $parameter The array containing the parameters to be validated.
     * @throws InvalidArgumentException If the parameter is invalid or if the ID field is not a number type.
     * @return void
     */
    public static function validateDeleteProductToCartRequest(array $parameter): void
    {
        self::validateDELETERequest($parameter);

        foreach ($parameter as $key => $value) {
            if (!in_array($key, self::$requiredParameters)) {
                throw new InvalidArgumentException($key . ' is not a valid parameter.');
            }
        }

        if (isset($parameter['id']) && !is_numeric($parameter['id'])) {
            throw new InvalidArgumentException('ID field must be a number type');
        }
    }
}
