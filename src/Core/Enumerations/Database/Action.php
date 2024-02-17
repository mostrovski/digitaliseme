<?php

namespace Digitaliseme\Core\Enumerations\Database;

enum Action: string
{
    case Select = 'SELECT';
    case Insert = 'INSERT';
    case Update = 'UPDATE';
    case Delete = 'DELETE';
}
