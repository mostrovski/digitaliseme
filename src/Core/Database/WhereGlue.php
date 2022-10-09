<?php

namespace Digitaliseme\Core\Database;

enum WhereGlue: string
{
    case And = 'AND';
    case Or = 'OR';
}
