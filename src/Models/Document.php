<?php

namespace Digitaliseme\Models;

use Digitaliseme\Core\ORM\Meta\ModelAttribute;
use Digitaliseme\Core\ORM\Model;

class Document extends Model
{
    #[ModelAttribute(protectedOnCreate: true, protectedOnUpdate: true)]
    public int $id;
    #[ModelAttribute]
    public string $title;
    #[ModelAttribute]
    public string $type;
    #[ModelAttribute]
    public string $issue_date;
    #[ModelAttribute]
    public ?int $agent_id;
    #[ModelAttribute]
    public ?int $storage_id;
    #[ModelAttribute(protectedOnUpdate: true)]
    public int $user_id;
}
