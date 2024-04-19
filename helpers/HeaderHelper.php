<?php

class HeaderHelper
{
    public static function setHeaders()
    {
        header("Access-Control-Allow-Origin: http://localhost:5173");
        header('Access-Control-Allow-Credentials: true');
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        header('Content-Type: application/json');
    }
}
