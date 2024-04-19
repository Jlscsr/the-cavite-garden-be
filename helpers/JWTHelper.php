<?php
require_once dirname(__DIR__) . '/config/load_env.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTHelper
{
    private $secret_key;
    private $hash_algorithm;

    public function __construct()
    {
        $this->secret_key = $_ENV['JWT_SECRET_KEY'];
        $this->hash_algorithm = $_ENV['JWT_HASH_ALGORITHM'];
    }

    public function getSecretKey()
    {
        return $this->secret_key;
    }

    public function encodeData($data)
    {
        $token = JWT::encode($data, $this->secret_key, $this->hash_algorithm);
        return $token;
    }

    public function decodeData($token)
    {
        $data = JWT::decode($token, new Key($this->secret_key, $this->hash_algorithm));
        return $data;
    }
}
