<?php

namespace Helpers;

class HeaderHelper
{

    /**
     * Sends preflight headers for CORS if the request method is OPTIONS.
     *
     * This function checks if the request method is OPTIONS and sends the necessary headers
     * for Cross-Origin Resource Sharing (CORS) if it is. The headers set include:
     * - Access-Control-Allow-Origin: http://localhost:5173
     * - Access-Control-Allow-Methods: GET, POST, PUT, DELETE
     * - Access-Control-Allow-Headers: Content-Type, X-Requested-With
     * - Access-Control-Allow-Credentials: true
     * - Access-Control-Expose-Headers: Content-Length
     *
     * If the request method is OPTIONS, the function sets the HTTP response code to 200 and
     * exits the script.
     *
     * @throws None
     * @return void
     */
    public static function SendPreflighthHeaders(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {

            header("Access-Control-Allow-Origin: http://localhost:5173");
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
            header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");
            header("Access-Control-Allow-Credentials: true");
            header("Access-Control-Expose-Headers: Content-Length");

            http_response_code(200);
            exit;
        }
    }

    /**
     * Sets the response headers for the current request.
     *
     * This function sets the necessary headers for the response to be sent. It sets the
     * `Access-Control-Allow-Origin` header to allow requests from `http://localhost:5173`,
     * the `Access-Control-Allow-Credentials` header to allow credentials, the
     * `Access-Control-Allow-Methods` header to allow the methods `GET`, `POST`, `PUT`, and
     * `DELETE`, and the `Access-Control-Allow-Headers` header to allow the headers
     * `Content-Type` and `X-Requested-With`. It also sets the `Content-Type` header to
     * `application/json`.
     *
     * @throws None
     * @return void
     */
    public static function setResponseHeaders(): void
    {
        header("Access-Control-Allow-Origin: http://localhost:5173");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");
        header("Content-Type: application/json");
    }
}
