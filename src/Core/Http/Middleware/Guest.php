<?php

namespace Digitaliseme\Core\Http\Middleware;

use Digitaliseme\Core\Contracts\Middleware;
use Digitaliseme\Core\Contracts\Response;
use Digitaliseme\Core\Http\Request;

class Guest implements Middleware
{
    public static function handle(Request $request): Request|Response
    {
        if (auth()->isIntact()) {
            flash()->error('You are logged in');

            return redirectResponse('/');
        }

        return $request;
    }
}
