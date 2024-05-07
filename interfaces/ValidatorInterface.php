<?php

namespace Interfaces;

interface ValidatorInterface
{
    public static function validateGETParameter(array $parameter);
    public static function validatePOSTPayload(array $payload);
    public static function validatePUTPayload(array $payload);
    public static function validateDELETEParameter(array $parameter);
}
