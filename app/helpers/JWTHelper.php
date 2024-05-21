<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use RuntimeException;
use UnexpectedValueException;

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
     * Encodes the given data into a JSON Web Token (JWT) string.
     *
     * @param array $data The data to be encoded.
     * @return string The encoded JWT string.
     */
    public function encodeDataToJWT(array $data): string
    {
        $token = JWT::encode($data, $this->secret_key, $this->hash_algorithm);
        return $token;
    }


    /**
     * Decodes a JSON Web Token (JWT) and returns its data as an array.
     *
     * @param string $token The JWT to decode.
     * @return array The decoded JWT data.
     */
    public function decodeJWTData(string $token): object
    {
        $data = JWT::decode($token, new Key($this->secret_key, $this->hash_algorithm));
        return $data;
    }

    /**
     * Validates a JSON Web Token (JWT) and checks if it is still valid.
     *
     * @param string $token The JWT to be validated.
     * @return bool Returns true if the token is valid and not expired, false otherwise.
     * @throws RuntimeException If the token signature is invalid or if an unexpected value is encountered.
     */
    public function validateToken(string $token): bool
    {
        try {
            $data = $this->decodeJWTData($token);
            $expiryDate = $data->expiry_date;

            if ($expiryDate < time()) {
                return false;
            }

            return true;
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            throw new RuntimeException($e->getMessage());
        } catch (UnexpectedValueException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }
}
