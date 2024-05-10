<?php

namespace Validators;

use Validators\RequestValidator;

use InvalidArgumentException;

class CategoriesValidator extends RequestValidator
{
    static $requiredParameters = ['id'];
    static $requiredFields = [
        'categoryName' => '/^[a-zA-Z\s]{3,}$/',
        'categoryDescription' => '/^.{10,}$/'
    ];

    public static function validateGetCategoriesByParameter(array $parameter): void
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
    }

    public static function validateAddCategoryRequest(array $payload): void
    {
        self::validatePOSTRequest($payload);
        self::checkRequiredFields($payload, self::$requiredFields);
        self::checkFieldsPattern($payload, self::$requiredFields);
    }

    public static function validateEditCategoryRequest(array $parameter, array $payload)
    {
        self::validatePUTRequest($parameter, $payload);
        self::checkRequiredFields($payload, self::$requiredFields);
        self::checkFieldsPattern($payload, self::$requiredFields);
    }

    public static function validateDeleteCategoryRequest(array $parameter)
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
