<?php

namespace App\Validators;

use InvalidArgumentException;

use App\Validators\RequestValidator;

class CategoriesValidator extends RequestValidator
{
    static $requiredParameters = ['id'];
    static $requiredFields = [
        'categoryName' => '/^[a-zA-Z\s]{3,}$/',
        'categoryDescription' => '/^.{10,}$/'
    ];

    /**
     * Validates the given GET request parameter for getting categories by parameter.
     *
     * @param array $parameter The GET request parameter to validate.
     * @throws InvalidArgumentException If the parameter is empty or not an array, or if the ID field is not a number type.
     * @return void
     */
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

    /**
     * Validates the given payload for adding a new category.
     *
     * @param array $payload The data for the new category.
     * @throws InvalidArgumentException If the payload is invalid.
     * @return void
     */
    public static function validateAddCategoryRequest(array $payload): void
    {
        self::validatePOSTRequest($payload);
        self::checkRequiredFields($payload, self::$requiredFields);
        self::checkFieldsPattern($payload, self::$requiredFields);
    }

    /**
     * Validates the edit category request.
     *
     * This function validates the given PUT request parameter and payload for editing a category. It first calls the `validatePUTRequest` method to validate the parameter and payload. Then, it checks if the required fields in the payload match the specified patterns using the `checkRequiredFields` and `checkFieldsPattern` methods.
     *
     * @param array $parameter The PUT request parameter to validate.
     * @param array $payload The PUT request payload to validate.
     * @throws InvalidArgumentException If the parameter or payload is empty or not an array, or if the required fields in the payload do not match the specified patterns.
     * @return void
     */
    public static function validateEditCategoryRequest(array $parameter, array $payload): void
    {
        self::validatePUTRequest($parameter, $payload);
        self::checkRequiredFields($payload, self::$requiredFields);
        self::checkFieldsPattern($payload, self::$requiredFields);
    }

    /**
     * Validates the given DELETE request parameter for deleting a category.
     *
     * @param array $parameter The DELETE request parameter to validate.
     * @throws InvalidArgumentException If the parameter is empty or not an array, or if the ID field is not a number type.
     * @return void
     */
    public static function validateDeleteCategoryRequest(array $parameter): void
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
