<?php

namespace App\Helpers;

use App\Builders\ResponseBuilder;

class ResponseHelper
{
    /**
     * Sends a JSON response with the given data and status code.
     *
     * @param array $response The data to be encoded into JSON.
     * @param int $statusCode The HTTP status code for the response.
     * @return void
     */
    public static function sendJsonResponse(array $response, int $statusCode): void
    {
        http_response_code($statusCode);
        echo json_encode($response);
        return;
    }

    /**
     * Sets the response using the provided ResponseBuilder object.
     *
     * @param ResponseBuilder $builder The ResponseBuilder object used to build the response.
     * @return void
     */
    public static function setResponse(ResponseBuilder $builder): void
    {
        $data = $builder->build();
        $statusCode = $builder->getStatusCode();
        self::sendJsonResponse($data, $statusCode);
    }

    /**
     * Sends a success response with optional data and a custom message.
     *
     * @param array $data Optional data to include in the response. Default is an empty array.
     * @param string $message The message to include in the response.
     * @param int $statusCode The HTTP status code for the response. Default is 200.
     * @return void
     */
    public static function sendSuccessResponse(array $data = [], string $message = "", int $statusCode = 200): void
    {
        $builder = new ResponseBuilder($message, 'success', $statusCode);
        $builder->setData($data);
        self::setResponse($builder);
    }

    /**
     * Sends an unauthorized response with the given message and status code.
     *
     * @param string $message The message to include in the response.
     * @param int $statusCode The HTTP status code for the response. Default is 401.
     * @return void
     */
    public static function sendUnauthorizedResponse(string $message, int $statusCode = 401): void
    {
        $builder = new ResponseBuilder($message, 'unauthorized', $statusCode);
        self::setResponse($builder);
    }

    /**
     * Sends an error response with the given message and status code.
     *
     * @param string $message The message to include in the response.
     * @param int $statusCode The HTTP status code for the response. Default is 400.
     * @return void
     */
    public static function sendErrorResponse(string $message, int $statusCode = 400): void
    {
        $builder = new ResponseBuilder($message, 'failed', $statusCode);
        self::setResponse($builder);
    }
}
