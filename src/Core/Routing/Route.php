<?php

namespace Digitaliseme\Core\Routing;

class Route
{
    protected array $segments = ['/'];
    protected int $segmentCount = 1;
    protected string $key = '|';
    protected array $parameterValues = [];

    public function __construct(
        protected string $uri,
        protected string $method,
        protected string $controller,
        protected string $action,
        protected array $middleware = [],
    ) {
        $this->setUri();
        $this->setSegments();
        $this->setKey();
    }

    public static function define(
        string $uri,
        string $method,
        string $controller,
        string $action,
        array $middleware = [],
    ): static {
        return new static($uri, $method, $controller, $action, $middleware);
    }

    public function segments(): array
    {
        return $this->segments;
    }

    public function segmentCount(): int
    {
        return $this->segmentCount;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function controller(): string
    {
        return $this->controller;
    }

    public function action(): string
    {
        return $this->action;
    }

    public function middleware(): array
    {
        return $this->middleware;
    }

    public function params(): array
    {
        return $this->parameterValues;
    }

    public function setParams(array $params): void
    {
        $this->parameterValues = $params;
    }

    protected function setUri(): void
    {
        if ($this->uri === '/') {
            return;
        }

        $this->uri = trim($this->uri, '/');
    }

    protected function setSegments(): void
    {
        if ($this->uri === '/') {
            return;
        }

        $this->segments = explode('/', $this->uri);
        $this->segmentCount = count($this->segments);
    }

    protected function setKey(): void
    {
        $this->key = $this->method.'|'.$this->uri;
    }
}
