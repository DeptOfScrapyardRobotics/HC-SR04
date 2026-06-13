<?php

namespace DeptOfScrapyardRobotics\Sensors\HCSR04\HCSR04\Exceptions;

use Exception;

class HCSR04Exception extends Exception
{
    public static function invalidProperty(string $name): static
    {
        return new static("Invalid property $name");
    }

    public static function echoTimeout(int $timeoutUs): static
    {
        return new static("HC-SR04 ECHO pin did not respond within {$timeoutUs} µs timeout");
    }

    public static function unsupportedOperation(string $operation): static
    {
        return new static("HC-SR04 does not support {$operation} — it is a GPIO-only sensor with no register bus");
    }
}
