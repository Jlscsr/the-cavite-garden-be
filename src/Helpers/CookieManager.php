<?php

namespace App\Helpers;

class CookieManager
{
    private $isSecure;
    private $isHttpOnly;
    private $cookieName;
    private $sameSite;
    private $domain;

    public function __construct()
    {
        // Determine if the request is secure
        $this->isSecure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ||
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

        // Set SameSite based on scheme
        $this->sameSite = $this->isSecure ? 'None' : 'Strict';

        // Cookie settings
        $this->isHttpOnly = true;
        $this->cookieName = 'tcg_access_token';
        $this->domain = $this->isRunningOnLocalhost() ? 'the-cavite-garden-be.local' : 'agile-forest-86410-744466084125.herokuapp.com';
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
        $localHosts = ['localhost', '127.0.0.1', 'the-cavite-garden-be.local'];
        return in_array($host, $localHosts) || preg_match('/\.local$/', $host);
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

        setcookie($this->cookieName, $token, [
            'expires' => $expiryDate,
            'path' => '/',
            'domain' => $this->domain,
            'secure' => $this->isSecure,
            'httponly' => $this->isHttpOnly,
            'samesite' => $this->sameSite,
        ]);
    }

    /**
     * Resets the cookie header by deleting the cookie with the specified name.
     *
     * @return void
     */
    public function resetCookieHeader(): void
    {
        setcookie($this->cookieName, '', [
            'expires' => time() - 3600,
            'path' => '/',
            'domain' => $this->domain,
            'secure' => $this->isSecure,
            'httponly' => $this->isHttpOnly,
            'samesite' => $this->sameSite,
        ]);
    }

    /**
     * Extracts the access token from the cookie header.
     *
     * @return string|null The access token extracted from the cookie header, or null if not found.
     */
    public function extractAccessTokenFromCookieHeader(): ?string
    {
        $headers = getallheaders();

        if (isset($headers['Cookie'])) {
            $cookie = $headers['Cookie'];
            parse_str(str_replace('; ', '&', $cookie), $cookies);
            return $cookies[$this->cookieName] ?? null;
        }

        return null;
    }

    /**
     * Validates the presence of the cookie in the headers.
     *
     * @return array|bool Returns true if the cookie is found, or an error array otherwise.
     */
    public function validateCookiePresence(): array|bool
    {
        $headers = getallheaders();

        if (!isset($headers['Cookie']) || strpos($headers['Cookie'], $this->cookieName . '=') === false) {
            return ['status' => 'failed', 'message' => 'Cookie not found'];
        }

        return ['status' => 'success', 'cookie' => $headers['Cookie']];
    }
}
