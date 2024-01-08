<?php

namespace Digitaliseme\Core\Contracts;

abstract class Response
{
    protected int $statusCode = 200;
    protected ?string $content = null;

    abstract public function send(): void;

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $value): static
    {
        $this->statusCode = $value;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $value): static
    {
        $this->content = $value;

        return $this;
    }
}
