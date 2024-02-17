<?php

namespace Digitaliseme\Core\ORM\Meta;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ModelAttribute
{
    public bool $protectedOnCreate;
    public bool $protectedOnUpdate;

    public function __construct(bool $protectedOnCreate = false, bool $protectedOnUpdate = false)
    {
        $this->protectedOnCreate = $protectedOnCreate;
        $this->protectedOnUpdate = $protectedOnUpdate;
    }
}
