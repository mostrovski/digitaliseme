<?php

namespace Digitaliseme\Core\Database;

use Digitaliseme\Core\Contracts\StaticConnection;
use Digitaliseme\Core\Exceptions\DatabaseException;
use PDO;

class MySQL extends StaticConnection
{
    protected array $configuration = [];

    /**
     * @throws DatabaseException
     */
    protected function config(): array
    {
        if (empty($this->configuration)) {
            $this->setConfig();
        }

        return $this->configuration;
    }

    /**
     * @throws DatabaseException
     */
    protected function setConfig(): void
    {
        $config = config('app.db') ?? throw DatabaseException::missingConfig();

        $this->configuration = [
            'host' => $config['host'] ?? throw DatabaseException::missingConfig('host'),
            'name' => $config['name'] ?? throw DatabaseException::missingConfig('name'),
            'charset' => $config['charset'] ?? throw DatabaseException::missingConfig('charset'),
            'user' => $config['user'] ?? throw DatabaseException::missingConfig('user'),
            'password' => $config['password'] ?? throw DatabaseException::missingConfig('password'),
        ];
    }

    /**
     * @throws DatabaseException
     */
    protected function dataSourceName(): string
    {
        $config = $this->config();

        return "mysql:host={$config['host']};dbname={$config['name']};charset={$config['charset']}";
    }

    /**
     * @throws DatabaseException
     */
    protected function user(): ?string
    {
        return $this->config()['user'];
    }

    /**
     * @throws DatabaseException
     */
    protected function password(): ?string
    {
        return $this->config()['password'];
    }

    protected function handlerOptions(): array
    {
        return [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ];
    }
}
