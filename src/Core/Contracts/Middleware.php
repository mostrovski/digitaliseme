<?php

namespace Digitaliseme\Core\Contracts;

use Digitaliseme\Core\Http\Request;

interface Middleware
{
    public static function handle(Request $request): Request|Response;
}