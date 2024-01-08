<?php

namespace Digitaliseme\Controllers;

use Digitaliseme\Core\Http\Responses\Redirect;
use Digitaliseme\Core\Http\Responses\View;

class DefaultController extends Controller
{
    public function index(): Redirect|View
    {
        if ($_SERVER['REQUEST_URI'] === '/') {
            return $this->redirect(auth()->isIntact() ? 'uploads/create' : 'login');
        }

        return $this->view('404', statusCode: 404);
    }
}
