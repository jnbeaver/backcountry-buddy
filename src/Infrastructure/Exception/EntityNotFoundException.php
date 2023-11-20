<?php

namespace App\Infrastructure\Exception;

use Exception;
use ReflectionClass;

class EntityNotFoundException extends Exception
{
    public function __construct(string $class, mixed $value, string $property = 'ID')
    {
        parent::__construct(
            sprintf(
                "%s with %s '%s' not found.",
                (new ReflectionClass($class))->getShortName(),
                $property,
                $value
            )
        );
    }
}
