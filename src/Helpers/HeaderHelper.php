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

            // header("Access-Control-Allow-Origin: https://localhost:5173");
            header("Access-Control-Allow-Origin: https://the-cavite-garden.web.app");

            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
            header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");
            header("Access-Control-Allow-Credentials: true");
            header("Access-Control-Expose-Headers: Content-Length");
            header('Content-Type: application/json');

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
        // header("Access-Control-Allow-Origin: https://localhost:5173");
        header("Access-Control-Allow-Origin: https://the-cavite-garden.web.app");

        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
        header("Access-Control-Allow-Headers: Referrer, Content-Type, Authorization");
        header('Access-Control-Allow-Credentials: true');
        header("Access-Control-Expose-Headers: Content-Length");
        header("Referrer-Policy: no-referrer");
        header('Content-Type: application/json');
    }
}
