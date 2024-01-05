<?php

namespace Digitaliseme\Core\Http;

use Digitaliseme\Core\Storage\File;

class Response
{
    protected int $statusCode = 200;
    protected ?string $content = null;
    protected ?string $redirectTo = null;
    protected ?File $file = null;

    public function send(): void
    {
        if ($this->isRedirect()) {
            header("Location: {$this->getRedirectTo()}");
            exit;
        }

        if ($this->isDownloadable()) {
            $this->file->download(); // TODO
            exit;
        }

        http_response_code($this->getStatusCode());
        echo $this->getContent();
    }

    public function isRedirect(): bool
    {
        $statusCode = $this->getStatusCode();

        return $statusCode >= 300 && $statusCode <= 399;
    }

    public function isDownloadable(): bool
    {
        return $this->getFile() instanceof File;
    }

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

    public function getRedirectTo(): ?string
    {
        return $this->redirectTo;
    }

    public function setRedirectTo(string $value): static
    {
        $this->redirectTo = $value;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(File $file): static
    {
        $this->file = $file;

        return $this;
    }
}
