<?php

namespace Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use Helpers\ResponseHelper;
use RuntimeException;
use UnexpectedValueException;

require_once dirname(__DIR__) . '/config/LoadEnvVariables.php';
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

    /**
     * Encodes the given data into a JSON Web Token (JWT) using the specified secret key and hash algorithm.
     *
     * @param mixed $data The data to be encoded into a JWT.
     * @return string The encoded JWT.
     */
    public function encodeDataToJWT($data)
    {
        $token = JWT::encode($data, $this->secret_key, $this->hash_algorithm);
        return $token;
    }

    /**
     * Decodes the given JWT token and returns the decoded data.
     *
     * @param string $token The JWT token to be decoded.
     * @throws Exception If there is an error decoding the token.
     * @return mixed The decoded data from the JWT token.
     */
    public function decodeJWTData($token)
    {
        $data = JWT::decode($token, new Key($this->secret_key, $this->hash_algorithm));
        return $data;
    }

    /**
     * Validates the authenticity and expiration of the JWT token.
     *
     * @param mixed $token The JWT token to be validated.
     * @throws \Firebase\JWT\SignatureInvalidException If the token signature is invalid.
     * @return bool Returns true if the token is valid, false otherwise.
     */
    public function validateToken($token)
    {
        try {
            $data = $this->decodeJWTData($token);
            $expiry_date = $data->expiry_date;

            if ($expiry_date < time()) {
                return false;
            }

            return true;
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            throw new RuntimeException('Invalid Token Signature');
        } catch (UnexpectedValueException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }
}
