<?php

require_once dirname(__DIR__) . '/config/load_env.php';

function db_connect()
{
    try {
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

        $dsn = "mysql:host=$host;dbname=$database";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $pdo = new \PDO($dsn, $username, $Password, $options);

        if ($pdo) {
            return $pdo;
        }
    } catch (PDOException $e) {
        die("Database Connection failed: " . $e->getMessage());
    }
}
