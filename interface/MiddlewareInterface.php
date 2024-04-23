<?php

interface MiddlewareInterface
{
    public function validateCookiePresence();
    public function verifyUserRole();
    public function validateToken();
}
