<?php

namespace Digitaliseme\Core;

use Digitaliseme\Core\Contracts\Response;
use Digitaliseme\Core\Http\Request;
use Digitaliseme\Core\Routing\Route;
use Digitaliseme\Core\Routing\Router;
use Digitaliseme\Core\Session\CSRF;
use Digitaliseme\Core\Session\Errors;
use Digitaliseme\Core\Session\Flash;
use Digitaliseme\Core\Session\OldInput;
use Digitaliseme\Core\Session\RedirectData;
use RuntimeException;
use Throwable;

class Application
{
    private static ?self $instance = null;

    private string $root;
    private string $configPath;
    private array $config = [];
    private Router $router;

    private Response $response;

    final private function __construct()
    {
        $this->setRoot();
        $this->setConfigPath();
        $this->setConfig();
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
        $this->setRouter();
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

    public function handleRequest(): Response
    {
        $request = Request::resolve();

        if ($request->method() !== 'GET' && $request->missingValidToken()) {
            return redirectResponse('403');
        }

        $route = $this->router->match($request);

        if (! $route instanceof Route) {
            return redirectResponse('404');
        }

        if (in_array('auth', $route->middleware(), true) &&
            auth()->isMissing()
        ) {
            flash()->error('You must be logged in to access this page');
            return redirectResponse('login');
        }

        try {
            $response = call_user_func_array(
                [new ($route->controller()), $route->action()],
                $route->params()
            );

            if (! $response instanceof Response) {
                throw new RuntimeException('Controller action must return a Response');
            }
        } catch (Throwable $e) {
            logger()->error($e->getMessage());
            return redirectResponse('500');
        }

        return $response;
    }

    public function root(): string
    {
        return $this->root;
    }

    public function config(): array
    {
        return $this->config;
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
        $this->router = Router::register();
    }

    private function configFiles(): array
    {
        return array_filter(
            scandir($this->configPath),
            static fn ($file) => str_ends_with($file, '.php')
        );
    }
}
