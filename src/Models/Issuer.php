<?php

namespace Digitaliseme\Models;

use Digitaliseme\Core\ORM\Meta\ModelAttribute;
use Digitaliseme\Core\ORM\Model;

class Issuer extends Model
{
    #[ModelAttribute(protectedOnCreate: true, protectedOnUpdate: true)]
    public int $id;
    #[ModelAttribute]
    public string $name;
    #[ModelAttribute]
    public string $email;
    #[ModelAttribute]
    public string $phone;
}
