<?php

use Helpers\CookieManager;
use Helpers\ResponseHelper;
use Helpers\JWTHelper;

class BaseMiddleware implements MiddlewareInterface
{

    public function __construct()
    {
        $this->jwt = new JWTHelper();
        $this->cookieManager = new CookieManager($this->jwt);
    }

    public function validateCookiePresence()
    {
        $this->cookieManager->validateCookiePresence();

        $this->validateToken();
    }

    public function validateToken()
    {
        $token = $this->cookieManager->extractAccessTokenFromCookieHeader();

        $is_token_valid = $this->jwt->validateToken($token);

        if (!$is_token_valid) {
            ResponseHelper::sendUnauthorizedResponse('Unauthorized');
            return;
        }
    }

    public function verifyUserRole()
    {
        //
    }
}
