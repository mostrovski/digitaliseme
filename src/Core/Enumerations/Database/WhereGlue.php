<?php

namespace Digitaliseme\Core\Enumerations\Database;

enum WhereGlue: string
{
    case And = 'AND';
    case Or = 'OR';
}
