<?php

namespace Digitaliseme\Exceptions;

use Exception;

final class FileException extends Exception
{
    public static function delete(): self
    {
        return new self('Failed to delete the file.');
    }

    public static function deleteRecord(): self
    {
        return new self('Failed to delete the file record.');
    }
}
