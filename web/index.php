<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Router;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
try {
    $uri = $_SERVER['REQUEST_URI'];
    $path = parse_url($uri, PHP_URL_PATH);

    $app = new Router();
    $app->run($path);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    echo "Error: " . $e->getMessage();
}