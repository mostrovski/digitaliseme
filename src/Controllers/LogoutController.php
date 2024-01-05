<?php

namespace Digitaliseme\Controllers;

use Digitaliseme\Core\Http\Response;

class LogoutController extends Controller
{
    public function index(): Response
    {
        if (auth()->isMissing()) {
            flash()->info('You are not logged in');
            return redirectResponse('/');
        }

        auth()->clear();
        flash()->success('You have successfully been logged out');
        return redirectResponse('/');
    }
}
