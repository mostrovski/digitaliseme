<?php

namespace Digitaliseme\Core\Traits;

use ReflectionClass;

trait Reflectable
{
    protected function reflection(): ReflectionClass
    {
        return new ReflectionClass(static::class);
    }
}
