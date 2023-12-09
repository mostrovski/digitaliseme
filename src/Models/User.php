<?php

namespace Digitaliseme\Models;

use Digitaliseme\Core\ORM\Meta\ModelAttribute;
use Digitaliseme\Core\ORM\Meta\Setter;
use Digitaliseme\Core\ORM\Model;

class User extends Model
{
    #[ModelAttribute(protectedOnCreate: true, protectedOnUpdate: true)]
    public int $id;
    #[ModelAttribute]
    public string $username;
    #[ModelAttribute]
    public string $first_name;
    #[ModelAttribute]
    public string $last_name;
    #[ModelAttribute]
    public string $email;
    #[
        ModelAttribute(protectedOnUpdate: true),
        Setter(methodName: 'convert')
    ]
    public string $password;

    protected function convert(string $password): string {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}
