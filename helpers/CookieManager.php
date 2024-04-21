<?php

namespace Helpers;

use Helpers\ResponseHelper;

class CookieManager
{
    private $jwt;
    private $is_secure;
    private $is_http_only;
    private $cookie_name;

    public function __construct($jwt)
    {
        $this->jwt = $jwt;
        $this->is_secure = true;
        $this->is_http_only = true;
        $this->cookie_name = 'tcg_access_token';
    }

    public function setCookiHeader($token, $expiry_date)
    {
        setcookie($this->cookie_name, $token, $expiry_date, '/', '', $this->is_secure, $this->is_http_only);
    }

    public function resetCookieHeader()
    {
        setcookie($this->cookie_name, '', time() - 3600, '/', '', $this->is_secure, $this->is_http_only);
    }

    public function getCookieHeader()
    {
        $headers = getallheaders();

        $this->validateCookiePressence();

        $token = $headers['Cookie'];
        $token = str_replace("tcg_access_token=", "", $token);

        return $token;
    }

    public function validateCookiePressence()
    {
        $headers = getallheaders();

        if (!isset($headers['Cookie'])) {
            ResponseHelper::sendErrorResponse('Missing Cookie', 400);
            exit;
        }
    }

    public function validateToken($token)
    {
        $data = $this->jwt->decodeData($token);

        $expiry_date = $data->expiry_date;

        if ($expiry_date < time()) {
            return false;
        }

        return true;
    }
}
