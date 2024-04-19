<?php
require_once dirname(__DIR__) . '/helpers/JWTHelper.php';

class BaseMiddleware
{
    protected $jwt;
    protected $required_role;

    public function __construct($required_role)
    {
        $this->jwt = new JWTHelper();
        $this->required_role = $required_role;
    }

    public function verifyUser()
    {
        $headers = getallheaders();

        if (!isset($headers['Cookie'])) {
            return false;
        }

        $token = $headers['Cookie'];
        $token = str_replace("tcg_access_token=", "", $token);

        $decoded_token = $this->jwt->decodeData($token);

        /* Check if token is valid */
        if (!isset($decoded_token->id)) {
            return false;
        }

        /* Check if token is expired */
        $expiry_time = $decoded_token->expiry_date;

        if ($expiry_time < time()) {
            return false;
        }

        /* Check if user has required role */
        $role = $decoded_token->role;

        if ($role != $this->required_role) {
            return false;
        }

        return true;
    }
}
