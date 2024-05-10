<?php

namespace Middlewares;

use Helpers\CookieManager;
use Helpers\JWTHelper;

use Interfaces\MiddlewareInterface;

use RuntimeException;

class BaseMiddleware implements MiddlewareInterface
{
    private $requiredRole;
    private $jwt;
    private $cookieManager;

    public function __construct($requiredRole)
    {
        $this->jwt = new JWTHelper();
        $this->cookieManager = new CookieManager();
        $this->requiredRole = $requiredRole;
    }

    public function validateRequest()
    {
        $this->checkCookiePresence();
    }

    public function checkCookiePresence()
    {
        try {
            $response = $this->cookieManager->validateCookiePressence();

            if (is_array($response) && isset($response['status']) && ($response['status'] === 'failed')) {
                throw new RuntimeException($response['message']);
                exit;
            }

            $this->validateToken($response);
        } catch (RuntimeException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    public function validateToken($cookieHeader)
    {
        $response = $this->cookieManager->extractAccessTokenFromCookieHeader($cookieHeader);

        if (is_array($response) && isset($response['status']) && ($response['status'] === 'failed')) {
            throw new RuntimeException($response['message']);
            exit;
        }

        $isTokenValid = $this->jwt->validateToken($response['token']);

        if (!$isTokenValid) {
            $this->cookieManager->resetCookieHeader();
            throw new RuntimeException('Token Invalid. Please Login again.');
            exit;
        }

        $this->verifyUserRole($this->jwt->decodeJWTData($response['token']));
    }

    public function verifyUserRole($decodedToken)
    {
        $userCurrentRole = $decodedToken->role;

        if ($this->requiredRole === 'both') {
            return;
        }

        if ($userCurrentRole !== $this->requiredRole) {
            throw new RuntimeException('Unauthorized');
            exit;
        }
    }
}
