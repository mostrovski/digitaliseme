<?php

namespace Digitaliseme\Core\Database\Contracts;

use PDO;
use PDOException;

abstract class StaticConnection implements Connection
{
    private static ?StaticConnection $instance = null;
    private PDO $connection;

    /**
     * @throws PDOException
     */
    final private function __construct()
    {
        $this->connection = new PDO(
            $this->dataSourceName(),
            $this->user(),
            $this->password(),
            $this->handlerOptions(),
        );
    }

    final public static function connect(): static
    {
        if (self::$instance === null) {
            self::$instance = new static;
        }

        return self::$instance;
    }

    final public function handler(): PDO
    {
        return $this->connection;
    }

    abstract protected function dataSourceName(): string;

    abstract protected function user(): ?string;

    abstract protected function password(): ?string;

    abstract protected function handlerOptions(): ?array;
}
