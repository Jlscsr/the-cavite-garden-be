<?php

namespace Interfaces;

interface MiddlewareInterface
{
    public function checkCookiePresence();
    public function validateToken();
    public function verifyUserRole(array $decodedToken);
}
