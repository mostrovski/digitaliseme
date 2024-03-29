<?php

namespace Digitaliseme\Core;

use Digitaliseme\Core\Contracts\Middleware;
use Digitaliseme\Core\Http\Middleware\ValidToken;
use Digitaliseme\Core\Routing\Router;
use Digitaliseme\Core\Session\CSRF;
use Digitaliseme\Core\Session\Errors;
use Digitaliseme\Core\Session\Flash;
use Digitaliseme\Core\Session\OldInput;
use Digitaliseme\Core\Session\RedirectData;

class Application
{
    private static ?self $instance = null;

    private string $root;
    private string $configPath;
    private array $config = [];
    private Router $router;
    private array $middleware = [
        ValidToken::class,
    ];

    final private function __construct()
    {
        $this->setRoot();
        $this->setConfigPath();
        $this->setConfig();
        $this->setRouter();
    }

    public static function resolve(): self
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function start(): void
    {
        CSRF::handler()->generateToken();
        OldInput::handler()->set();
    }

    public function terminate(): void
    {
        Errors::handler()->clear();
        Flash::handler()->clear();
        OldInput::handler()->clear();
        RedirectData::handler()->clear();
    }

    public function root(): string
    {
        return $this->root;
    }

    public function config(): array
    {
        return $this->config;
    }

    public function router(): Router
    {
        return $this->router;
    }

    /**
     * @return Middleware[]
     */
    public function middleware(): array
    {
        return $this->middleware;
    }

    private function setRoot(): void
    {
        $this->root = dirname(__DIR__, 2);
    }

    private function setConfigPath(): void
    {
        $this->configPath = $this->root.'/config';
    }

    private function setConfig(): void
    {
        $files = $this->configFiles();

        foreach ($files as $configFile) {
            $key = str_replace('.php', '', $configFile);
            $this->config[$key] = require $this->configPath.'/'.$configFile;
        }
    }

    private function setRouter(): void
    {
        $this->router = new Router($this->config['routes']);
    }

    private function configFiles(): array
    {
        return array_filter(
            scandir($this->configPath),
            static fn ($file) => str_ends_with($file, '.php')
        );
    }
}
