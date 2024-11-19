<?php

namespace Config;

use Config\EnvironmentLoader;

use PDO;
use PDOException;

class DatabaseConnection
{
    public static function connect(): ?PDO
    {

        try {
            $environment = 'production';

            if ($environment == "production") {
                // Use the Heroku JawsDB URL directly
                $url = parse_url(getenv('JAWSDB_URL'));

                $host = $url['host'];
                $username = $url['user'];
                $password = $url['pass'];
                $database = ltrim($url['path'], '/'); // Removes the leading slash from the database name
            } else {
                $host = '127.0.0.1';
                $username = 'root';
                $password = '';
                $database = 'the-cavite-garden-db';
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
