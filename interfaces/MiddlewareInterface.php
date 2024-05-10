<?php

namespace Interfaces;

interface MiddlewareInterface
{
    public function checkCookiePresence();
    public function validateToken($cookieHeader);
    public function verifyUserRole(array $decodedToken);
}
