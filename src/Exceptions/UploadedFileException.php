<?php

namespace Digitaliseme\Exceptions;

use Exception;

final class UploadedFileException extends Exception
{
    public static function invalid(): self
    {
        return new self('The file is invalid.');
    }

    public static function empty(): self
    {
        return new self('The file is empty.');
    }

    public static function size(): self
    {
        return new self('The file is too big.');
    }

    public static function type(): self
    {
        return new self('The file type is not supported.');
    }
}
