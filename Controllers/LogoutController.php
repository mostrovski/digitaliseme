<?php
namespace Controllers;

use Core\Helper;
use Models\User;

class LogoutController extends Controller {
    public function index() {
        $_SESSION['flash'] = User::logOut();
        return Helper::redirect(HOME);
    }
}
?>