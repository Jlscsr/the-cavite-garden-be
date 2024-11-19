<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Router;

print_r($_GET);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
try {
    echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'];
    $uri = $_SERVER['REQUEST_URI'];
    $path = parse_url($uri, PHP_URL_PATH);

    $query = parse_url($uri, PHP_URL_QUERY);

    echo "Path: " . $path . "<br>";  
    echo "Query: " . $query . "<br>";  

    $app = new Router();
    $app->handleRequest($query);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    echo "Error: " . $e->getMessage();
}
