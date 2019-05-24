<?php
namespace Core;

class Page {
    // Renders the page based on the requested url
    protected static $controller = 'Controllers\DefaultController';
    protected static $action = 'index';
    protected static $params = [];

    public static function render() {
        self::defineController();
        self::instantiate(self::$controller);
        self::invoke(self::$controller, self::$action, self::$params);
    }

    protected static function defineController() {
        // Replace the default controller if url is set and valid
        $public = PUBLIC_ROUTES;
        $private = PRIVATE_ROUTES;
        $loggedIn = Helper::isUserLoggedIn();

        if (!isset($_GET['url'])) return;

        $request = self::transform($_GET['url']);

        if (!in_array($request['resource'], $public) &&
            !in_array($request['resource'], $private)) {
            return;
        }

        if (!$loggedIn && in_array($request['resource'], $private)) {
            $_SESSION['flash'] = AUTHENTICATION_ERROR;
            return Helper::redirect(HOME);
        }

        self::parse($request);
    }

    protected static function instantiate($controller) {
        self::$controller = new $controller;
    }

    protected static function invoke($controller, $action, $params) {
        call_user_func_array([$controller, $action], $params);
    }

    protected static function parse($request) {
        // Map requested url to controller, action, and parameters
        $controller = 'Controllers\\'.ucfirst($request['resource']);
        $controller .= 'Controller';
        self::$controller = $controller;

        if (!isset($request['action']) ||
            !method_exists(self::$controller, $request['action']))
        return;

        self::$action = $request['action'];
        self::$params = $request['parameters'];
    }

    protected static function transform($url) {
        // Turn the url into an associative array
        $url = rtrim($url, '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $url = explode('/', $url);

        return [
            'resource'   => $url[0],
            'action'     => $url[1] ?? NULL,
            'parameters' => array_slice($url, 2),
        ];
    }
}
?>