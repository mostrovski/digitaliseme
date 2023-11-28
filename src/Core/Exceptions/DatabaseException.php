<?php

namespace Digitaliseme\Core\Exceptions;

use Exception;

final class DatabaseException extends Exception
{
    public static function missingConfig(?string $key = null): self
    {
        if (empty($key)) {
            return new self('DB configuration is not set.');
        }

        return new self('DB configuration for '.$key.' is not specified.');
    }

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

    public static function sqlPreparationFailed(): self
    {
        return new self('SQL preparation failed.');
    }

    public static function statementNotSet(): self
    {
        return new self('SQL statement is not set.');
    }
}
