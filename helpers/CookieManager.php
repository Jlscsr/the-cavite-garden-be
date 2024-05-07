<?php

namespace Helpers;

use RuntimeException;

class CookieManager
{
    private $isSecure;
    private $isHttpOnly;
    private $cookieName;

    public function __construct()
    {
        $this->isSecure = true;
        $this->isHttpOnly = true;
        $this->cookieName = 'tcg_access_token';
    }

    /**
     * Sets a cookie with the given token and expiry date.
     *
     * @param string $token The token to set in the cookie.
     * @param int $expiryDate The expiry date of the cookie in Unix timestamp format.
     * @return void
     */
    public function setCookiHeader($token, $expiryDate)
    {
        setcookie($this->cookieName, $token, $expiryDate, '/', '', $this->isSecure, $this->isHttpOnly);
    }

    /**
     * Resets the cookie header by deleting the cookie with the specified name.
     *
     * This function sets the cookie with the name specified in the `$cookieName` property to an empty value,
     * with an expiry date set to one hour ago. The cookie is set to be accessible only on the current domain,
     * and it is flagged as secure if the `$isSecure` property is set to true. The cookie is also flagged as HTTP-only
     * if the `$isHttpOnly` property is set to true.
     *
     * @return void
     */
    public function resetCookieHeader()
    {
        setcookie($this->cookieName, '', time() - 3600, '/', '', $this->isSecure, $this->isHttpOnly);
    }

    /**
     * Extracts the access token from the cookie header.
     *
     * This function retrieves all the headers using the `getallheaders()` function and
     * validates the presence of the cookie using the `validateCookiePressence()` method.
     * It then extracts the access token from the cookie header by removing the prefix
     * "tcg_access_token=". The extracted token is returned.
     *
     * @return string The access token extracted from the cookie header.
     */
    public function extractAccessTokenFromCookieHeader()
    {
        $headers = getallheaders();

        $this->validateCookiePressence();

        $token = $headers['Cookie'];

        if (strpos($token, $this->cookieName) === false) {
            $this->resetCookieHeader();
            return ['status' => 'failed', 'message' => 'Access Token is Missing. Please Login Again'];
        }

        $token = str_replace("tcg_access_token=", "", $token);

        return ['token' => $token];
    }

    /**
     * Validates the presence of the cookie in the headers.
     *
     * This function retrieves all the headers using `getallheaders()`,
     * checks if the 'Cookie' header is set, and sends an error response
     * with a 400 status code if the cookie header is missing.
     *
     * @throws void
     * @return void
     */
    public function validateCookiePressence()
    {
        $headers = getallheaders();

        if (!isset($headers['Cookie'])) {
            return ['status' => 'failed', 'message' => 'Cookie header is missing'];
            exit;
        }
    }
}
