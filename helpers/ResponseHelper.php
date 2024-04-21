<?php

namespace Helpers;

class ResponseHelper
{

    /**
     * Sends a JSON response with the given data, message, status message, and status code.
     *
     * @param mixed $data The data to be included in the response (optional).
     * @param string $message The message to be included in the response.
     * @param string $status_message The status message to be included in the response.
     * @param int $status_code The HTTP status code to be set for the response.
     * @return void
     */
    public static function sendJsonResponse($data, $message, $status_message, $status_code)
    {
        $response = [
            'status' => $status_message,
            'message' => $message,
        ];

        if (!empty($data)) {
            $response['data'] = $data;
        }

        http_response_code($status_code);
        echo json_encode($response);
    }

    /**
     * Sends a success response with the given data, message, and status code.
     *
     * @param array $data The data to be included in the response (optional). Default is an empty array.
     * @param string $message The message to be included in the response.
     * @param int $status_code The HTTP status code to be set for the response. Default is 200.
     * @return void
     */
    public static function sendSuccessResponse($data = [], $message, $status_code = 200)
    {
        ResponseHelper::sendJsonResponse($data, $message, 'success', $status_code);
    }

    /**
     * Sends an unauthorized response with the given message and status code.
     *
     * @param string $message The message to be included in the response.
     * @param int $status_code The HTTP status code to be set for the response. Default is 401.
     * @return void
     */
    public static function sendUnauthorizedResponse($message, $status_code = 401)
    {
        ResponseHelper::sendJsonResponse([], $message, 'unauthorized', $status_code);
    }

    /**
     * Sends a database error response with the given message and status code.
     *
     * @param string $message The message to be included in the response.
     * @param int $status_code The HTTP status code to be set for the response. Default is 404.
     * @return void
     */
    public static function sendDatabaseErrorResponse($message, $status_code = 404)
    {
        ResponseHelper::sendJsonResponse([], $message, 'failed', $status_code);
    }

    /**
     * A description of the entire PHP function.
     *
     * @param string $message The message to be included in the response.
     * @param int $status_code The HTTP status code to be set for the response. Default is 400.
     * @return void
     */
    public static function sendErrorResponse($message, $status_code = 400)
    {
        ResponseHelper::sendJsonResponse([], $message, 'failed', $status_code);
    }

    /**
     * Sends a server error response with the given message and status code.
     *
     * @return void
     */
    public static function sendServerErrorResponse()
    {
        ResponseHelper::sendJsonResponse([], 'Internal Server Error', 'failed', 500);
    }
}
