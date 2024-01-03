<?php

namespace Digitaliseme\Controllers;

class LogoutController extends Controller
{
    public function index(): void
    {
        if (auth()->isMissing()) {
            flash()->info('You are not logged in');
            $this->redirect('/');
        }

        auth()->clear();
        flash()->success('You have successfully been logged out');
        $this->redirect('/');
    }
}
