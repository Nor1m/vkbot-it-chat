<?php

namespace App\Traits;

use App\Exceptions\ReadOnlyPropertyException;
use App\Exceptions\UndefinedPropertyException;

trait PropertyAccess
{
    /**
     * @param $name
     * @return mixed
     * @throws UndefinedPropertyException
     */
    public function __get($name)
    {
        if (method_exists($this, "get$name")) {
            return $this->{"get$name"}();
        }

        throw new UndefinedPropertyException(
            "Trying to get undefined property '$name' in class " . static::class
        );
    }

    /**
     * @param $name
     * @param $value
     * @throws UndefinedPropertyException
     * @throws ReadOnlyPropertyException
     */
    public function __set($name, $value): void
    {
        if (method_exists($this, "set$name")) {
            $this->{"set$name"}($value);
            return;
        } elseif (method_exists($this, "get$name")) {
            throw new ReadOnlyPropertyException("Trying to set read-only property '$name' in class " . static::class);
        }

        throw new UndefinedPropertyException(
            "Trying to set undefined property '$name' in class " . static::class
        );
    }
}