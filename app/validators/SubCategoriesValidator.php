<?php

namespace App\Validators;

use InvalidArgumentException;

use App\Validators\RequestValidator;

class SubCategoriesValidator extends RequestValidator
{
    static $requiredParameters = ['id'];
    static $requiredFields = [
        'categoryID' => [
            'format' => null,
            'errorMessage' => ''
        ],
        'subCategoryName' => [
            'format' => '/^[a-zA-Z\s]{3,}$/',
            'errorMessage' => 'Subcategory name must be at least 3 characters long and can only contain letters and spaces.',
            'required' => true,
        ],
        'subCategoryDescription' => [
            'format' => '/^.{10,}$/',
            'errorMessage' => 'Subcategory description must be at least 10 characters long.',
            'required' => true,
        ]
    ];

    /**
     * Validates the given parameter for the GET request in the SubCategoriesValidator class.
     *
     * @param array $parameter The parameter to be validated.
     * @throws InvalidArgumentException If the parameter is not a valid parameter or if the ID field is not a number type.
     * @return void
     */
    public static function validateGetSubCategoriesByParameter(array $parameter): void
    {
        self::validateGETRequest($parameter);

        foreach ($parameter as $key => $value) {
            if (!in_array($key, self::$requiredParameters)) {
                throw new InvalidArgumentException($key . ' is not a valid parameter.');
            }
        }
    }

    /**
     * Validates the addition of a subcategory based on the provided payload.
     *
     * @param array $payload The data for the new subcategory.
     * @throws InvalidArgumentException If the payload is invalid.
     * @return void
     */
    public static function validateAddSubCategoryRequest(array $payload): void
    {
        self::validatePOSTRequest($payload);
        self::checkRequiredFields($payload, self::$requiredFields);
        self::checkFieldsPattern($payload, self::$requiredFields);
    }

    /**
     * Validates the edit of a subcategory based on the provided parameter and payload.
     *
     * @param array $parameter The parameter to be validated.
     * @param array $payload The data for the edited subcategory.
     * @throws Some_Exception_Class description of exception
     * @return Some_Return_Value
     */
    public static function validateEditSubCategoryRequest(array $parameter, array $payload): void
    {
        self::validatePUTRequest($parameter, $payload);
        self::checkRequiredFields($payload, self::$requiredFields);
        self::checkFieldsPattern($payload, self::$requiredFields);
    }

    /**
     * Validates the delete subcategory request by performing the following checks:
     * - Calls the validateDELETERequest function to validate the request.
     * - Checks if all the required parameters are present.
     * - Checks if the ID field is a number type.
     *
     * @param array $parameter The array containing the parameters to be validated.
     * @throws InvalidArgumentException If the parameter is invalid or if the ID field is not a number type.
     * @return void
     */
    public static function validateDeleteSubCategoryRequest(array $parameter): void
    {
        self::validateDELETERequest($parameter);

        foreach ($parameter as $key => $value) {
            if (!in_array($key, self::$requiredParameters)) {
                throw new InvalidArgumentException($key . ' is not a valid parameter.');
            }
        }
    }
}
