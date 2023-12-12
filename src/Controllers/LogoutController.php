<?php

namespace Digitaliseme\Controllers;

use Digitaliseme\Core\Helper;
use Digitaliseme\Models\User;

class LogoutController extends Controller
{
    public function index(): void
    {
        if (! isset($_SESSION["loggedin"])) {
            flash()->info(config('app.messages.info.LOGIN_NOT'));
            $this->redirect('/');
        }

        unset($_SESSION["loggedin"], $_SESSION["loggedinName"], $_SESSION["loggedinID"]);

        flash()->success(config('app.messages.info.LOGOUT_OK'));
        $this->redirect('/');
    }
}
