<?php
namespace Controllers;

use Core\Helper;

class DefaultController extends Controller {
    // Loads public default, private default, or 404
    public function index() {
        if (isset($_GET['url'])) return $this->view('404', ['title'=>'404']);
        $loggedIn = Helper::isUserLoggedIn();
        return $loggedIn ? $this->show('private') : $this->show('public');
    }

    protected function show($route) {
        $defaults = DEFAULT_ROUTES;
        $controller = new $defaults[$route]['controller'];
        $method = $defaults[$route]['method'];
        return $controller->$method();
    }
}
?>