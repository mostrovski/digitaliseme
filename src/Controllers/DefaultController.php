<?php

namespace Digitaliseme\Controllers;

class DefaultController extends Controller
{
    public function index(): void
    {
        if ($_SERVER['REQUEST_URI'] !== '/') {
            $this->view('404', ['title' => '404']);
        } else {
            auth()->isIntact() ? $this->show('private') : $this->show('public');
        }
    }

    protected function show($route)
    {
        $defaults = config('app.routes.default');
        $controller = new $defaults[$route]['controller'];
        $method = $defaults[$route]['method'];
        return $controller->$method();
    }
}
