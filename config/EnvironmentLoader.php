<?php

namespace Config;

use Dotenv\Dotenv;

class EnvironmentLoader
{
    public static function load(): void
    {
        require_once dirname(__DIR__) . '/vendor/autoload.php';

        $dotenv = Dotenv::createImmutable(dirname(__DIR__));
        $dotenv->load();
    }
}
