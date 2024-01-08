<?php

namespace Digitaliseme\Core\Http\Responses;

use Digitaliseme\Core\Contracts\Response;

class View extends Response
{
    public function send(): void
    {
        http_response_code($this->getStatusCode());
        echo $this->getContent();
    }
}
