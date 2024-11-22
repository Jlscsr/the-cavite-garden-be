<?php

namespace App\Middlewares;

use RuntimeException;

use App\Interfaces\MiddlewareInterface;

use App\Helpers\CookieManager;
use App\Helpers\JWTHelper;
use App\Helpers\ResponseHelper;

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

    /**
     * Validates the request by checking the presence of a cookie.
     *
     * This function calls the `checkCookiePresence()` method to verify if a cookie
     * is present in the request. If the cookie is not found, a `RuntimeException`
     * is thrown.
     *
     * @throws RuntimeException If the cookie is not present in the request.
     * @return void
     */
    public function validateRequest(): void
    {
        try {
            $this->checkCookiePresence();
        } catch (RuntimeException $e) {
            $this->cookieManager->resetCookieHeader();
            ResponseHelper::sendUnauthorizedResponse($e->getMessage());
            exit;
        }
    }

    /**
     * Checks the presence of a cookie in the request and validates the token.
     *
     * This function validates the presence of a cookie in the request by calling the
     * `validateCookiePresence()` method of the `cookieManager` object. If the cookie
     * is not found, a `RuntimeException` is thrown. If the cookie is found, the
     * `validateToken()` method is called to validate the token.
     *
     * @throws RuntimeException If the cookie is not present in the request or the token is invalid.
     * @return void
     */
    public function checkCookiePresence(): void
    {
        try {
            $response = $this->cookieManager->validateCookiePresence();

            if (is_array($response) && isset($response['status']) && ($response['status'] === 'failed')) {
                throw new RuntimeException($response['message']);
                exit;
            }

            $this->validateToken($response);
        } catch (RuntimeException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    /**
     * Validates the token in the cookie header.
     *
     * This function takes a cookie header as input and validates the token present in it. 
     * It first extracts the access token from the cookie header using the `extractAccessTokenFromCookieHeader` 
     * method of the `cookieManager` object. If the token is not found or is invalid, a `RuntimeException` 
     * is thrown. If the token is valid, it verifies the user role using the `decodeJWTData` method of the 
     * `jwt` object.
     *
     * @param string $cookieHeader The cookie header containing the token.
     * @throws RuntimeException If the token is not found or is invalid.
     * @return void
     */
    public function validateToken(string $cookieHeader): void
    {

        $response = $this->cookieManager->extractAccessTokenFromCookieHeader($cookieHeader);

        if (is_array($response) && isset($response['status']) && ($response['status'] === 'failed')) {
            throw new RuntimeException($response['message']);
        }

        $isTokenValid = $this->jwt->validateToken($response['token']);

        if (!$isTokenValid) {
            $this->cookieManager->resetCookieHeader();
            throw new RuntimeException('Token Invalid. Please Login again.');
        }

        $this->verifyUserRole($this->jwt->decodeJWTData($response['token']));
    }

    /**
     * Verifies the user role based on the decoded token.
     *
     * @param array $decodedToken The decoded token containing user role information.
     * @throws RuntimeException If the user role is not authorized.
     * @return void
     */
    public function verifyUserRole(object $decodedToken): void
    {
        $userCurrentRole = $decodedToken->role;

        if ($this->requiredRole === 'both') {
            return;
        }

        if ($userCurrentRole !== $this->requiredRole) {
            throw new RuntimeException('Unauthorized. Your role is not authorized for this action.');
        }
    }
}
