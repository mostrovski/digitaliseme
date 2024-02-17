<?php

namespace Digitaliseme\Core\ORM\Meta;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Setter
{
    public string $methodName;

    public function __construct(string $methodName)
    {
        $this->methodName = $methodName;
    }
}
