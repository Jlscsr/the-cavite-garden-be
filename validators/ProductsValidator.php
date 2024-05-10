<?php

namespace Validators;

use Validators\RequestValidator;

use InvalidArgumentException;

class ProductsValidator extends RequestValidator
{
    static $requiredParameters = ['id', 'category'];
    static $requiredFields = [
        'productPhotoURL' => null,
        'productName' => '/^[a-zA-Z0-9\s]{3,}$/',
        'productCategory' => '/^[a-zA-Z\s]{3,}$/',
        'productSubCategory' => '/^([a-zA-Z\s]{3,})?$/',
        'productPrice' => '/^\d+(\.\d{1,2})?$/',
        'productSize' => '/^.*$/',
        'productStock' => '/^\d+$/',
        'productDescription' => '/^.{10,}$/'
    ];

    public static function validateGetProductRequestsByParameter(array $parameter): void
    {
        self::validateGETRequest($parameter);

        foreach ($parameter as $key => $value) {
            if (!in_array($key, self::$requiredParameters)) {
                throw new InvalidArgumentException($key . ' is not a valid parameter.');
            }
        }

        if (isset($parameter['id']) && !is_numeric($parameter['id'])) {
            throw new InvalidArgumentException('ID field must be a number type');
        }

        if (isset($parameter['category']) && !is_numeric($parameter['category'])) {
            throw new InvalidArgumentException('Category field must be a number type');
        }
    }

    public static function validateAddProductRequest(array $payload): void
    {
        self::validatePOSTRequest($payload);
        self::checkRequiredFields($payload, self::$requiredFields);
        self::checkFieldsPattern($payload, self::$requiredFields);
    }

    public static function validateEditProductRequest(array $parameter, array $payload)
    {
        self::validatePUTRequest($parameter, $payload);
        self::checkRequiredFields($payload, self::$requiredFields);
        self::checkFieldsPattern($payload, self::$requiredFields);
    }

    public static function validateDeleteProductRequest(array $parameter)
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
