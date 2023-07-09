<?php

namespace App\Enums;

use ReflectionClass;

abstract class AbstractEnum
{
    /**
     * Get all the class constant.
     */
    public static function all(): array
    {
        return (new ReflectionClass(static::class))->getConstants();
    }

    /**
     * Get the specific value of a constant.
     *
     * @return mixed
     */
    public static function get(string $key)
    {
        return (new ReflectionClass(static::class))->getConstant($key);
    }
}
