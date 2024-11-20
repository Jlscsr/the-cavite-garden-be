<?php

namespace App\Validators;

use InvalidArgumentException;

use App\Validators\RequestValidator;

class ProductsValidator extends RequestValidator
{
    static $requiredParameters = ['id', 'category'];
    static $requiredFields = [
        'productVideoURL' => [
            'format' => null,
            'erroMessage' => '',
            'required' => true,
        ],
        'imageSequenceFolderURL' => [
            'format' => null,
            'errorMessage' => '',
            'required' => true,
        ],
        'productName' => [
            'format' => '/^[a-zA-Z0-9\s]{3,}$/',
            'errorMessage' => 'Product name must be at least 3 characters long and contain only letters, numbers, and spaces.',
            'required' => true,
        ],
        'productCategory' => [
            'format' => '/^[a-zA-Z\s]{3,}$/',
            'errorMessage' => 'Product category must be at least 3 characters long and contain only letters and spaces.',
            'required' => true,
        ],
        'productSubCategory' => [
            'format' => '/^([a-zA-Z\s]{3,})?$/',
            'errorMessage' => 'Product sub-category must be at least 3 characters long and contain only letters and spaces.',
            'required' => false,
        ],
        'productPrice' => [
            'format' => '/^\d+(\.\d{1,2})?$/',
            'errorMessage' => 'Product price must be a number with up to 2 decimal places.',
            'required' => true,
        ],
        'productSize' => [
            'format' => '/^(small|medium|large)$/i', // Allows only "small", "medium", "large", or "fit"
            'errorMessage' => 'Invalid product size format. Allowed values are small, medium, large',
            'required' => false,
        ],

        'productStock' => [
            'format' => '/^\d+$/',
            'errorMessage' => 'Product stock must be a number.',
            'required' => true,
        ],
        'productDescription' => [
            'format' => '/^.{10,}$/',
            'errorMessage' => 'Product description must be at least 10 characters long.',
            'required' => true,
        ]
    ];

    /**
     * Validates the GET request parameters for getting product requests.
     *
     * @param array $parameter The array containing the parameters to be validated.
     * @throws InvalidArgumentException If the parameter is invalid.
     * @return void
     */
    public static function validateGetProductRequestsByParameter(array $parameter): void
    {
        self::validateGETRequest($parameter);

        foreach ($parameter as $key => $value) {
            if (!in_array($key, self::$requiredParameters)) {
                throw new InvalidArgumentException($key . ' is not a valid parameter.');
            }
        }
    }

    /**
     * Validates the given payload for adding a new product.
     *
     * This function validates the payload for adding a new product by performing the following checks:
     * - Calls the validatePOSTRequest function to validate the payload.
     * - Checks if all the required fields in the payload are present.
     * - Checks if the values of the payload match the required formats.
     *
     * @param array $payload The data for the new product.
     * @throws InvalidArgumentException If the payload is invalid.
     * @return void
     */
    public static function validateAddProductRequest(array $payload): void
    {
        self::validatePOSTRequest($payload);
        self::checkRequiredFields($payload, self::$requiredFields);
        self::checkFieldsPattern($payload, self::$requiredFields);
    }

    /**
     * Validates the edit product request.
     *
     * This function validates the edit product request by performing the following checks:
     * - Calls the validatePUTRequest function to validate the request.
     * - Checks if all the required fields in the payload are present.
     * - Checks if the values of the payload match the required formats.
     *
     * @param array $parameter The parameters for the edit request.
     * @param array $payload The data to be edited.
     * @throws InvalidArgumentException If the parameter or payload is invalid.
     * @return void
     */
    public static function validateEditProductRequest(array $parameter, array $payload): void
    {
        self::validatePUTRequest($parameter, $payload);
        self::checkRequiredFields($payload, self::$requiredFields);
        self::checkFieldsPattern($payload, self::$requiredFields);
    }

    /**
     * Validates the delete product request by performing the following checks:
     * - Calls the validateDELETERequest function to validate the request.
     * - Checks if all the required parameters are present.
     * - Checks if the ID field is a number type.
     *
     * @param array $parameter The array containing the parameters to be validated.
     * @throws InvalidArgumentException If the parameter is invalid.
     * @return void
     */
    public static function validateDeleteProductRequest(array $parameter): void
    {
        self::validateDELETERequest($parameter);

        foreach ($parameter as $key => $value) {
            if (!in_array($key, self::$requiredParameters)) {
                throw new InvalidArgumentException($key . ' is not a valid parameter.');
            }
        }
    }
}
