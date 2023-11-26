<?php

namespace Digitaliseme\Controllers;

use Digitaliseme\Core\Helper;
use Digitaliseme\Models\User;

class LogoutController extends Controller
{
    public function index(): void
    {
        if (! isset($_SESSION["loggedin"])) {
            $_SESSION['flash'] = config('app.messages.info.LOGIN_NOT');
            Helper::redirect(config('app.url'));
        }

        unset($_SESSION["loggedin"], $_SESSION["loggedinName"], $_SESSION["loggedinID"]);

        $_SESSION['flash'] = config('app.messages.info.LOGOUT_OK');
        Helper::redirect(config('app.url'));
    }
}
