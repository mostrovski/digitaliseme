<?php

namespace Digitaliseme\Core\Http\Middleware;

use Digitaliseme\Core\Contracts\Middleware;
use Digitaliseme\Core\Contracts\Response;
use Digitaliseme\Core\Http\Request;

class Authenticated implements Middleware
{
    public static function handle(Request $request): Request|Response
    {
        if (auth()->isMissing()) {
            flash()->error('You are not logged in');

            return redirectResponse('login');
        }

        return $request;
    }
}
