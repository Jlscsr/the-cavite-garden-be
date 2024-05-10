<?php

namespace Interfaces;

interface ValidatorInterface
{
    public static function validateGETRequest(array $parameter);
    public static function validatePOSTRequest(array $payload);
    public static function validatePUTRequest(array $parameter, array $payload);
    public static function validateDELETERequest(array $parameter);
}
