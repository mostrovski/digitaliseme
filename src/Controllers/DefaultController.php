<?php

namespace Digitaliseme\Controllers;

use Digitaliseme\Core\Http\Responses\Redirect;
use Digitaliseme\Core\Http\Responses\View;

class DefaultController extends Controller
{
    public function index(): Redirect|View
    {
        return match ($this->request()->uri()) {
            '/' => $this->redirect(auth()->isIntact() ? 'uploads/create' : 'login'),
            '/403' => $this->view('403', statusCode: 403),
            '/500' => $this->view('500', statusCode: 500),
            default => $this->view('404', statusCode: 404),
        };
    }
}
