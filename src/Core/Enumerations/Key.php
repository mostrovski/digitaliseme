<?php

namespace Digitaliseme\Core\Enumerations;

enum Key: string
{
    case AltMethod = '_r_method';
    case Authenticated = 'authenticated';
    case CsrfToken = '_s_token';
    case Errors = 'errors';
    case Flash = 'flash';
    case OldInput = 'old';
    case RedirectData = 'redirect-data';
}
