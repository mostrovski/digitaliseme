<?php

namespace Digitaliseme\Exceptions;

use Exception;

final class DatabaseException extends Exception
{
    public static function missingTable(): self
    {
        return new self('DB table is not specified.');
    }

    public static function missingAction(): self
    {
        return new self('Action (SELECT, INSERT, etc.) is not specified.');
    }

    public static function missingInsertData(): self
    {
        return new self('Data for the insert is not specified.');
    }

    public static function missingUpdateData(): self
    {
        return new self('Data for the update is not specified.');
    }

    public static function handlerNotSet(): self
    {
        return new self('DB handler is not set.');
    }

    public static function statementNotSet(): self
    {
        return new self('SQL statement is not set.');
    }
}
