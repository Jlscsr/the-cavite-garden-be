<?php

namespace Config;

use Config\EnvironmentLoader;

use PDO;
use PDOException;

class DatabaseConnection
{
    public static function connect(): ?PDO
    {
        require_once dirname(__DIR__) . '/vendor/autoload.php';
        EnvironmentLoader::load();

        try {
            $environment = $_ENV['ENVIRONMENT'];

            if ($environment == "production") {
                $host = $_ENV['PROD_DB_HOST'];
                $username = $_ENV['PROD_DB_USERNAME'];
                $password = $_ENV['PROD_DB_PASSWORD'];
                $database = $_ENV['PROD_DB_NAME'];
            } else {
                $host = $_ENV['DEV_DB_HOST'];
                $username = $_ENV['DEV_DB_USERNAME'];
                $password = $_ENV['DEV_DB_PASSWORD'];
                $database = $_ENV['DEV_DB_NAME'];
            }

            $dsn = "mysql:host=$host;dbname=$database";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            return new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            die("Database Connection failed: " . $e->getMessage());
        }
    }
}
