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
        // Comment if running in localhost, uncomment if not
        $this->isSecure = $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? true : false;
        $this->sameSite = $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'None' : 'Strict';

        $this->isHttpOnly = true;
        $this->cookieName = 'tcg_access_token';

        // Dynamically set `isSecure` and `sameSite` based on environment
        /* if ($this->isRunningOnLocalhost()) {
            $this->isSecure = false;
        } else {
            $this->isSecure = true;
            $this->sameSite = 'None'; // Required for cross-site cookies
        } */
    }

    /**
     * Sets a cookie with the given token and expiry date.
     *
     * @param string $token The token to set in the cookie.
     * @param int $expiryDate The expiry date of the cookie in Unix timestamp format.
     * @return void
     */
    public function setCookieHeader(string $token, int $expiryDate): void
    {
        $this->resetCookieHeader();

        // For Production
         $isLocalhost = $this->isRunningOnLocalhost();

        setcookie($this->cookieName, $token, [
            'expires' => $expiryDate,
            'path' => '/',
            'domain' => 'agile-forest-86410-744466084125.herokuapp.com',
            'secure' => !$isLocalhost,
            'httponly' => $this->isHttpOnly,
            'samesite' => $this->sameSite,
        ]);

        /* For Localhosting */
        // setcookie($this->cookieName, $token, $expiryDate, '/', '', false, $this->isHttpOnly);
    }

    /**
     * Determines if the current environment is localhost.
     *
     * @return bool
     */
    private function isRunningOnLocalhost(): bool
    {
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        // Match common localhost scenarios and *.local domains
        $localHosts = ['localhost', '127.0.0.1'];
        if (preg_match('/\.local$/', $host)) {
            return true;
        }

        return in_array($host, $localHosts);
    }

    /**
     * Resets the cookie header by deleting the cookie with the specified name.
     *
     * @return void
     */
    public function resetCookieHeader(): void
    {
        // setcookie('tcg_access_token', '', time() - 3600, '/', '', $this->isSecure, $this->isHttpOnly);

        // For deployment
        setcookie('tcg_access_token', '', time() - 3600, '/', 'agile-forest-86410-744466084125.herokuapp.com', $this->isSecure, $this->isHttpOnly);
    }

    /**
     * Extracts the access token from the provided cookie header.
     *
     * @param string|array $cookieHeader The cookie header containing the access token.
     * @return array The extracted access token or an error message if the token is missing.
     */
    public function extractAccessTokenFromCookieHeader(string | array $cookieHeader): array
    {
        $token = $cookieHeader;

        if (strpos($token, $this->cookieName) === false) {
            $this->resetCookieHeader();
            return ['status' => 'failed', 'message' => 'Access Token is Missing. Please Login Again'];
        }

        $token = str_replace($this->cookieName . "=", "", $token) ?? '';

        return ['token' => $token];
    }

    /**
     * Validates the presence of a cookie in the request headers.
     *
     * @return array|string Returns an array with 'status' and 'message' if the header is missing, otherwise returns the value of the 'Cookie' header.
     */
    public function validateCookiePresence(): array|string
    {
        $headers = getallheaders();

        if (!isset($headers['Cookie'])) {
            return ['status' => 'failed', 'message' => 'Cookie header is missing'];
        }

        return $headers['Cookie'];
    }
}
