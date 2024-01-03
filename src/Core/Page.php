<?php

namespace Digitaliseme\Core;

use Digitaliseme\Controllers\DefaultController;

class Page
{
    protected static string $controller = DefaultController::class;
    protected static string $action = 'index';
    protected static array $params = [];

    public static function render(): void
    {
        self::defineController();
        self::invoke();
    }

    protected static function defineController(): void
    {
        if ($_SERVER['REQUEST_URI'] === '/') {
            return;
        }

        // Replace the default controller if url is set and valid
        $public = config('app.routes.public');
        $private = config('app.routes.private');
        $loggedIn = auth()->isIntact();
        $request = self::transform($_SERVER['REQUEST_URI']);

        if (! in_array($request['resource'], $public, true) &&
            ! in_array($request['resource'], $private, true)
        ) {
            return;
        }

        if (! $loggedIn && in_array($request['resource'], $private, true)) {
            flash()->error('You have to be logged in to visit this page');
            redirect('/');
        }

        self::parse($request);
    }

    protected static function invoke(): void
    {
        call_user_func_array([new self::$controller, self::$action], self::$params);
    }

    /**
     * @param array<string,mixed> $request
     */
    protected static function parse(array $request): void
    {
        // Map requested url to controller, action, and parameters
        self::$controller = 'Digitaliseme\\Controllers\\'.ucfirst($request['resource']).'Controller';

        if (! isset($request['action']) ||
            ! method_exists(self::$controller, $request['action'])
        ) {
            return;
        }

        self::$action = $request['action'];
        self::$params = $request['parameters'];
    }

    protected static function transform(string $url): array
    {
        // Turn the url into an associative array
        $url = rtrim($url, '/');
        $url = ltrim($url, '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $urlParts = explode('/', $url);

        return [
            'resource' => $urlParts[0],
            'action' => $urlParts[1] ?? NULL,
            'parameters' => array_slice($urlParts, 2),
        ];
    }
}
