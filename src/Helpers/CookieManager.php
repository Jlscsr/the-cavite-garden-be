<?php

namespace App\Helpers;

class CookieManager
{
    private $isSecure;
    private $isHttpOnly;
    private $cookieName;
    private $sameSite;

    public function __construct()
    {
        $this->isSecure = $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? true : false;
        $this->isHttpOnly = true;
        $this->cookieName = 'tcg_access_token';
        $this->sameSite = $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'None' : 'Strict';
    }

    /**
     * Sets a cookie with the given token and expiry date.
     *
     * @param string $token The token to set in the cookie.
     * @param int $expiryDate The expiry date of the cookie in Unix timestamp format.
     * @return void
     */
    public function setCookiHeader(string $token, int $expiryDate): void
    {
        self::resetCookieHeader();
        setcookie($this->cookieName, $token, [
            'expires' => $expiryDate,
            'path' => '/',
            'domain' => 'agile-forest-86410-744466084125.herokuapp.com',  // Specify the domain if needed
            'secure' => $this->isSecure,  // Set Secure for HTTPS requests only
            'httponly' => $this->isHttpOnly,
            'samesite' => $this->sameSite,  
        ]);
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
    public function resetCookieHeader(): void
    {
        setcookie($this->cookieName, '', time() - 3600, '/', '', $this->isSecure, $this->isHttpOnly);
    }


    /**
     * Extracts the access token from the provided cookie header.
     *
     * @param string $cookieHeader The cookie header containing the access token.
     * @return array The extracted access token or an error message if the token is missing.
     */
    public function extractAccessTokenFromCookieHeader(string | array $cookieHeader): array
    {
        $token = $cookieHeader;

        if (strpos($token, $this->cookieName) === false) {
            $this->resetCookieHeader();
            return ['status' => 'failed', 'message' => 'Access Token is Missing. Please Login Again'];
        }

        $token = str_replace("tcg_access_token=", "", $token) ?? '';

        return ['token' => $token];
    }


    /**
     * Validates the presence of a cookie in the request headers.
     *
     * This function checks if the 'Cookie' header is present in the request headers.
     * If the header is missing, it returns an array with 'status' set to 'failed' and 'message' set to 'Cookie header is missing'.
     * If the header is present, it returns the value of the 'Cookie' header.
     *
     * @return array|string Returns an array with 'status' and 'message' if the header is missing, otherwise returns the value of the 'Cookie' header.
     */
    public function validateCookiePressence(): array | string
    {
        $headers = getallheaders();

        if (!isset($headers['Cookie'])) {
            return ['status' => 'failed', 'message' => 'Cookie header is missing'];
        }

        return $headers['Cookie'];
    }
}
