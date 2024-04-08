<?php
require_once './config/load_env.php';

$environment = $_ENV['ENVIRONMENT'];

if ($environment == "production") {
    $host = $_ENV['PROD_DB_HOST'];
    $username = $_ENV['PROD_DB_USERNAME'];
    $Password = $_ENV['PROD_DB_PASSWORD'];
    $database = $_ENV['PROD_DB_NAME'];
} else {
    $host = $_ENV['DEV_DB_HOST'];
    $username = $_ENV['DEV_DB_USERNAME'];
    $Password = $_ENV['DEV_DB_PASSWORD'];
    $database = $_ENV['DEV_DB_NAME'];
}

$connection = new mysqli($host, $username, $Password, $database);

// Check connection
if ($connection->connect_error) {
    die("Database Connection failed: " . $connection->connect_error);
}
