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

        if (isset($parameter['id']) && !is_numeric($parameter['id'])) {
            throw new InvalidArgumentException('ID field must be a number type');
        }

        if (isset($parameter['category']) && !is_numeric($parameter['category'])) {
            throw new InvalidArgumentException('Category field must be a number type');
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

        if (isset($parameter['id']) && !is_numeric($parameter['id'])) {
            throw new InvalidArgumentException('ID field must be a number type');
        }
    }
}
