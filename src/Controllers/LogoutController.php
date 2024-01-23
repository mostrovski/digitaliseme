<?php

namespace Digitaliseme\Controllers;

use Digitaliseme\Core\Http\Responses\Redirect;

class LogoutController extends Controller
{
    public function index(): Redirect
    {
        auth()->clear();
        flash()->success('You have successfully been logged out');

        return $this->redirect('/');
    }
}
