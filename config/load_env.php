<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';;

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();
