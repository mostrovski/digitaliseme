<?php
namespace Core;

class Page {
    // Renders the page based on the requested url
    protected static $controller = 'Controllers\DefaultController';
    protected static $method = 'index';
    protected static $params = [];

    public static function render() {
        self::defineController();
        self::instantiate(self::$controller);
        self::invoke(self::$controller, self::$method, self::$params);
    }

    protected static function defineController() {
        // Replace the default controller if url is set and valid
        $public = PUBLIC_ROUTES;
        $private = PRIVATE_ROUTES;
        $loggedIn = Helper::isUserLoggedIn();

        if (!isset($_GET['url'])) return;

        $requested = self::transform($_GET['url']);

        if (!in_array($requested['resource'], $public) &&
            !in_array($requested['resource'], $private)) {
            return;
        }

        if (!$loggedIn && in_array($requested['resource'], $private)) {
            $_SESSION['flash'] = AUTHENTICATION_ERROR;
            return Helper::redirect(HOME);
        }

        self::parse($requested);
    }

    protected static function instantiate($controller) {
        self::$controller = new $controller;
    }

    protected static function invoke($controller, $method, $params) {
        call_user_func_array([$controller, $method], $params);
    }

    protected static function parse($requested) {
        // Map requested url to controllers, methods, and parameters
        $controller = 'Controllers\\'.ucfirst($requested['resource']);
        $controller .= 'Controller';
        self::$controller = $controller;

        if (!isset($requested['method']) ||
            !method_exists(self::$controller, $requested['method']))
        return;

        self::$method = $requested['method'];
        self::$params = $requested['parameters'];
    }

    protected static function transform($url) {
        // Turn the url into an associative array
        $url = rtrim($url, '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $url = explode('/', $url);

        return [
            'resource'   => $url[0],
            'method'     => $url[1] ?? NULL,
            'parameters' => array_slice($url, 2),
        ];
    }
}
?>