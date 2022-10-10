<?php

namespace Digitaliseme\Controllers;

use Digitaliseme\Core\Helper;
use Digitaliseme\Models\User;

class LogoutController extends Controller {
    public function index() {
        $_SESSION['flash'] = User::logOut();
        return Helper::redirect(config('app.url'));
    }
}
