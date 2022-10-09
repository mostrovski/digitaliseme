<?php

namespace Digitaliseme\Core\Database;

enum Connector: string
{
    case And = 'AND';
    case Or = 'OR';
}
