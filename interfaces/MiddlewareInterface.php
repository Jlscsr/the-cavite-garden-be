<?php

namespace Interfaces;

interface MiddlewareInterface
{
    public function checkCookiePresence();
    public function validateToken(string $cookieHeader);
    public function verifyUserRole(object $decodedToken);
}
