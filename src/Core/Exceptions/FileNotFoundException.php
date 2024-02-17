<?php

namespace Digitaliseme\Core\Exceptions;

use Exception;

class FileNotFoundException extends Exception
{
    protected $message = 'File not found';
}
