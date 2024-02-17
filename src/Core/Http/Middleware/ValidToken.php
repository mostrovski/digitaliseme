<?php

namespace Digitaliseme\Core\Http\Middleware;

use Digitaliseme\Core\Contracts\Middleware;
use Digitaliseme\Core\Contracts\Response;
use Digitaliseme\Core\Http\Request;

class ValidToken implements Middleware
{
    public static function handle(Request $request): Request|Response
    {
        if ($request->method() !== 'GET' && $request->missingValidToken()) {
            return redirectResponse('403');
        }

        return $request;
    }
}
