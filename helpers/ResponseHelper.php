<?php
// Create a response helper for API responses

class ResponseHelper
{
    public static function sendSuccessResponse($data = null, $message, $status_code = 200)
    {
        http_response_code($status_code);

        $response = [
            'status' => 'success',
            'code' => $status_code,
            'message' => $message,
            'data' => $data ?? null,
        ];

        echo json_encode($response);
    }

    public static function sendErrorResponse($message, $status_code = 400)
    {
        http_response_code($status_code);
        echo json_encode(["error" => $message]);
    }

    public static function sendUnauthrizedResponse($message, $status_code = 200)
    {
        http_response_code($status_code);

        $response = [
            'status' => 'unauthorized',
            "message" => $message,
        ];

        echo json_encode($response);
    }
}
