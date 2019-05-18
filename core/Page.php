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

        $url = self::transform($_GET['url']);

        if (!in_array($url[0], $public) &&
            !in_array($url[0], $private)) {
            return;
        }

        if (!$loggedIn && in_array($url[0], $private)) {
            $_SESSION['flash'] = AUTHENTICATION_ERROR;
            return Helper::redirect(HOME);
        }

        self::parse($url);
    }

    protected static function instantiate($controller) {
        self::$controller = new $controller;
    }

    protected static function invoke($controller, $method, $params) {
        call_user_func_array([$controller, $method], $params);
    }

    protected static function parse($url) {
        // Map url parts to controllers, methods, and parameters
        $controller = 'Controllers\\'.ucfirst($url[0]).'Controller';
        self::$controller = $controller;
        unset($url[0]);

        if (!isset($url[1])) return;
        if (!method_exists(self::$controller, $url[1])) return;

        self::$method = $url[1];
        unset($url[1]);

        self::$params = $url ? array_values($url) : [];
    }

    protected static function transform($url) {
        // Turn the url into an array
        $url = rtrim($url, '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $url = explode('/', $url);
        return $url;
    }
}
?>