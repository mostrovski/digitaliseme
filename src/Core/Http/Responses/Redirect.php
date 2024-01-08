<?php

namespace Digitaliseme\Core\Http\Responses;

use Digitaliseme\Core\Contracts\Response;
use Digitaliseme\Core\Session\RedirectData;

class Redirect extends Response
{
    protected int $statusCode = 302;
    public function __construct(
        protected string $redirectTo,
        protected array $data = [],
    ) {}


    public function send(): void
    {
        RedirectData::handler()->set($this->getData());
        header("Location: {$this->getRedirectTo()}");
        exit;
    }

    public function getRedirectTo(): string
    {
        return $this->redirectTo;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function with(array $data): static
    {
        $this->data = $data;

        return $this;
    }
}
