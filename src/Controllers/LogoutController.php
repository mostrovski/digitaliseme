<?php

namespace Digitaliseme\Controllers;

use Digitaliseme\Core\Http\Responses\Redirect;

class LogoutController extends Controller
{
    public function index(): Redirect
    {
        if (auth()->isMissing()) {
            flash()->info('You are not logged in');
            return $this->redirect('/');
        }

        auth()->clear();
        flash()->success('You have successfully been logged out');
        return $this->redirect('/');
    }
}
