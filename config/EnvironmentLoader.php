<?php

namespace Config;

use Dotenv\Dotenv;

class EnvironmentLoader
{
    public static function load(): void
    {
        require_once __DIR__ . '/vendor/autoload.php';

        $dotenv = Dotenv::createImmutable(__DIR__);
        $dotenv->load();
    }
}
