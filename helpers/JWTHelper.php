<?php

namespace Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use Helpers\ResponseHelper;

require_once dirname(__DIR__) . '/config/load_env.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

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

    public function validateAndEncodeToken($token)
    {
        try {
            $data = $this->decodeData($token);

            $expiry_date = $data->expiry_date;

            if ($expiry_date < time()) {
                ResponseHelper::sendUnauthorizedResponse('Token is expired');
                exit;
            }

            return $data;
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            ResponseHelper::sendUnauthorizedResponse('Invalid Token Signature');
            exit();
        }
    }
}
