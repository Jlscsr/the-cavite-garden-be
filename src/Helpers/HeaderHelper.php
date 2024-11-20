<?php

namespace App\Helpers;

class HeaderHelper
{
    /**
     * List of allowed origins for CORS.
     */
    private static $allowedOrigins = [
        "https://the-cavite-garden.web.app",
        "http://localhost:5173"
    ];

    /**
     * Sends preflight headers for CORS if the request method is OPTIONS.
     *
     * This function checks if the request method is OPTIONS and sends the necessary headers
     * for Cross-Origin Resource Sharing (CORS) if it is.
     *
     * @throws None
     * @return void
     */
    public static function SendPreflightHeaders(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

            /* if (in_array($origin, self::$allowedOrigins)) {
                header("Access-Control-Allow-Origin: $origin");
            } */
           header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
           header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");
           header("Access-Control-Allow-Credentials: true");
           header("Access-Control-Expose-Headers: Content-Length");
           header("Access-Control-Allow-Origin: https://the-cavite-garden.web.app");

            http_response_code(200);
            exit;
        }
    }

    /**
     * Sets the response headers for the current request.
     *
     * This function sets the necessary headers for the response to be sent.
     *
     * @throws None
     * @return void
     */
    public static function SetResponseHeaders(): void
    {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

        /* if (in_array($origin, self::$allowedOrigins)) {
            header("Access-Control-Allow-Origin: $origin");
        } */

       header("Access-Control-Allow-Credentials: true");
       header("Cache-Control: no-cache, must-revalidate");
       header("Pragma: no-cache");
       header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
       header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");
       header("Content-Type: application/json");
       header("Access-Control-Allow-Origin: https://the-cavite-garden.web.app");
    }
}
