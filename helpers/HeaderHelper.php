<?php

namespace Helpers;

class HeaderHelper
{
    public static function SendPreflighthHeaders(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {

            header("Access-Control-Allow-Origin: http://localhost:5173");
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
            header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");
            header("Access-Control-Allow-Credentials: true");
            header("Access-Control-Expose-Headers: Content-Length");

            http_response_code(200);
            exit();
        }
    }
    public static function setResponseHeaders(): void
    {
        header("Access-Control-Allow-Origin: http://localhost:5173");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");
        header("Content-Type: application/json");
    }
}
