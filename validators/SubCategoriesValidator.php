<?php

namespace Validators;

use Validators\RequestValidator;

use InvalidArgumentException;

class SubCategoriesValidator extends RequestValidator
{
    static $requiredParameters = ['id'];
    static $requiredFields = [
        'categoryID' => null,
        'subCategoryName' => '/^[a-zA-Z\s]{3,}$/',
        'subCategoryDescription' => '/^.{10,}$/'
    ];

    public static function validateGetSubCategoriesByParameter(array $parameter): void
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

    public static function validateAddSubCategoryRequest(array $payload): void
    {
        self::validatePOSTRequest($payload);
        self::checkRequiredFields($payload, self::$requiredFields);
        self::checkFieldsPattern($payload, self::$requiredFields);
    }

    public static function validateEditSubCategoryRequest(array $parameter, array $payload)
    {
        self::validatePUTRequest($parameter, $payload);
        self::checkRequiredFields($payload, self::$requiredFields);
        self::checkFieldsPattern($payload, self::$requiredFields);
    }

    public static function validateDeleteSubCategoryRequest(array $parameter)
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
