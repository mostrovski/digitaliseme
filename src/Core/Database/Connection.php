<?php

namespace Digitaliseme\Core\Database;

use PDO;
use PDOException;

class Connection
{
    private static ?self $instance = null;

    private string $host = DB_HOST;
    private string $user = DB_USER;
    private string $password = DB_PASSWORD;
    private string $name = DB_NAME;
    private string $charset = DB_CHARSET;
    private PDO $connection;

    /**
     * @throws PDOException
     */
    final private function __construct()
    {
        $this->connection = new PDO(
            $this->dataSourceName(),
            $this->user,
            $this->password,
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

    public function handle(): PDO
    {
        return $this->connection;
    }

    private function dataSourceName(): string
    {
        return "mysql:host={$this->host};dbname={$this->name};charset={$this->charset}";
    }

    private function handlerOptions(): array
    {
        return [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ];
    }
}
