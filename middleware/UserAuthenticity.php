<?php
require_once dirname(__DIR__) . '/helpers/CookieManager.php';
require_once dirname(__DIR__) . '/helpers/JWTHelper.php';

class UserAuthenticity
{
    private $cookie_manager;
    private $jwt;
    public function __construct()
    {
        $this->jwt = new JWTHelper();
        $this->cookie_manager = new CookieManager($this->jwt);
    }

    public function checkUserAuthenticity()
    {
        $headers = getallheaders();
        if (!isset($headers['Cookie'])) {
            return false;
        }

        $token = $headers['Cookie'];
        $token = str_replace("tcg_access_token=", "", $token);

        try {
            $decoded_token = $this->jwt->decodeData($token);
            if (empty($decoded_token)) {
                return false;
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
