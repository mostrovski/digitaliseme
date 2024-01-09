<?php

namespace Digitaliseme\Core\Http;

use Digitaliseme\Core\Session\CSRF;

class Request
{
    private static ?self $instance = null;
    private string $uri;
    private string $query;
    private string $method;
    private array $data;
    private array $files;

    final private function __construct()
    {
        $this->setQuery();
        $this->setUri();
        $this->setData();
        $this->setFiles();
        $this->setMethod();
    }

    public static function resolve(): self
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function hasValidToken(): bool
    {
        $token = $this->get('_s_token', default: '');

        return ! empty($token) && (CSRF::handler()->token() === $token);
    }

    public function missingValidToken(): bool
    {
        return ! $this->hasValidToken();
    }

    public function is(string $uri): bool
    {
        return $this->uri() === $uri;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data()[$key] ?? $default;
    }

    public function query(): string
    {
        return $this->query;
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function data(): array
    {
        return $this->data;
    }

    public function files(): array
    {
        return $this->files;
    }

    private function setQuery(): void
    {
        $this->query = $_SERVER['QUERY_STRING'];
    }
    private function setUri(): void
    {
        $this->uri = str_replace('?'.$this->query(), '', $_SERVER['REQUEST_URI']);
    }

    private function setData(): void
    {
        $this->data = array_merge($_GET, $_POST);
    }

    private function setFiles(): void
    {
        $this->files = $_FILES;
    }

    private function setMethod(): void
    {
        $this->method = $_SERVER['REQUEST_METHOD'];

        if ($this->method !== 'POST') {
            return;
        }

        $altMethod = strtoupper ((string) $this->get('_r_method'));

        if (in_array($altMethod, ['PUT', 'PATCH', 'DELETE'], true)) {
            $this->method = $altMethod;
        }
    }
}
