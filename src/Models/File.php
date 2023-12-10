<?php

namespace Digitaliseme\Models;

use Digitaliseme\Core\ORM\Meta\ModelAttribute;
use Digitaliseme\Core\ORM\Model;

class File extends Model
{
    #[ModelAttribute(protectedOnCreate: true, protectedOnUpdate: true)]
    public int $id;
    #[ModelAttribute]
    public string $filename;
    #[ModelAttribute]
    public string $path;
    #[ModelAttribute]
    public ?int $document_id;
    #[ModelAttribute(protectedOnUpdate: true)]
    public int $user_id;

    public function publicPath(): string
    {
        return config('app.url').'storage/documents/'.$this->path;
    }

    public function fullPath(): string
    {
        return document_root().$this->path;
    }
}
