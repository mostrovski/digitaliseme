<?php

namespace Digitaliseme\Controllers;

use Digitaliseme\Core\Http\Response;

class DefaultController extends Controller
{
    public function index(): Response
    {
        if ($_SERVER['REQUEST_URI'] === '/') {
            return redirectResponse(auth()->isIntact() ? 'uploads/create' : 'login');
        }

        return viewResponse('404', statusCode: 404);
    }
}
