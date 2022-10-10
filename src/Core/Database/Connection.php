<?php

namespace Digitaliseme\Core\Database;

use PDO;
use PDOException;

class Connection
{
    private static ?self $instance = null;
    private PDO $connection;

    /**
     * @throws PDOException
     */
    final private function __construct()
    {
        $this->connection = new PDO(
            $this->dataSourceName(),
            config('app.db.user'),
            config('app.db.password'),
            $this->handlerOptions(),
        );
    }

    public static function resolve(): self
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function handler(): PDO
    {
        return $this->connection;
    }

    private function dataSourceName(): string
    {
        $config = config('app.db');

        return "mysql:host={$config['host']};dbname={$config['name']};charset={$config['charset']}";
    }

    private function handlerOptions(): array
    {
        return [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ];
    }
}
