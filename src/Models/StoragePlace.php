<?php

namespace Digitaliseme\Models;

use Digitaliseme\Core\ORM\Meta\ModelAttribute;
use Digitaliseme\Core\ORM\Model;

class StoragePlace extends Model
{
    #[ModelAttribute(protectedOnCreate: true, protectedOnUpdate: true)]
    public int $id;

    #[ModelAttribute]
    public string $place;
}
