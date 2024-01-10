<?php

namespace Digitaliseme\Core\Http;

enum Method: string
{
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case PATCH = 'PATCH';
    case DELETE = 'DELETE';
    case OPTIONS = 'OPTIONS';
    case HEAD = 'HEAD';

    public static function postAlternatives(): array
    {
        return [self::PUT->value, self::PATCH->value, self::DELETE->value];
    }
}
