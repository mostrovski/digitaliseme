<?php

namespace Digitaliseme\Controllers;

use Digitaliseme\Core\Helper;

class DefaultController extends Controller
{
    public function index()
    {
        if ($_SERVER['REQUEST_URI'] !== '/') {
            $this->view('404', ['title' => '404']);
        }
        $loggedIn = Helper::isUserLoggedIn();
        return $loggedIn ? $this->show('private') : $this->show('public');
    }

    protected function show($route)
    {
        $defaults = config('app.routes.default');
        $controller = new $defaults[$route]['controller'];
        $method = $defaults[$route]['method'];
        return $controller->$method();
    }
}
