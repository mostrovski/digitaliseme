<?php

namespace Digitaliseme\Exceptions;

use Exception;

class DatabaseException extends Exception
{
    public static function missingTable(): static
    {
        return new static('DB table is not specified.');
    }

    public static function missingAction(): static
    {
        return new static('Action (SELECT, INSERT, etc.) is not specified.');
    }

    public static function missingInsertData(): static
    {
        return new static('Data for the insert is not specified.');
    }

    public static function missingUpdateData(): static
    {
        return new static('Data for the update is not specified.');
    }

    public static function handlerNotSet(): static
    {
        return new static('DB handler is not set.');
    }

    public static function statementNotSet(): static
    {
        return new static('SQL statement is not set.');
    }
}
