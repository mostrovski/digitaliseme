<?php

namespace Digitaliseme\Core\Http\Responses;

use Digitaliseme\Core\Contracts\Response;
use Digitaliseme\Core\Storage\File;

class Download extends Response
{
    public function __construct(
        protected File $file,
        protected string $fileName = 'document',
    ) {}

    public function send(): void
    {
        ob_end_clean();
        header("Content-Type: {$this->file->mimeType()}");
        header("Content-Disposition: attachment; filename={$this->fileName}.{$this->file->extension()}");
        header("Content-Length: {$this->file->getInfo()->getSize()}");
        readfile($this->file->getPath());
    }
}
