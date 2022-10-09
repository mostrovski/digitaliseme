<?php

namespace Digitaliseme\Core\Database;

use Digitaliseme\Exceptions\DatabaseException;
use PDO;
use PDOException;
use PDOStatement;

// TODO: handle Exceptions
class DB {

    protected string $host = DB_HOST;
    protected string $user = DB_USER;
    protected string $password = DB_PASSWORD;
    protected string $name = DB_NAME;
    protected string $charset = DB_CHARSET;
    protected ?PDO $handler = null;
    protected ?PDOStatement $statement = null;
    protected Query $query;

    /**
     * Connect.
     *
     * @throws PDOException
     */
    final public function __construct() {
        $this->handler = new PDO(
            $this->dataSourceName(),
            $this->user,
            $this->password,
            $this->handlerOptions(),
        );

        $this->query = new Query;
    }

    /**
     * Disconnect.
     */
    final public function __destruct() {
        $this->statement = null;
        $this->handler = null;
    }

    public static function table(string $name): static
    {
        return (new static)->useTable($name);
    }

    public function useTable(string $name): static
    {
        $this->query->setTable($name);

        return $this;
    }

    public function select(string ...$columns): static
    {
        $this->query->setSelectables($columns);

        return $this;
    }

    public function where(string $column, string $operator, mixed $value, Connector $connector = Connector::And): static
    {
        if ($this->query->hasNoWhereClauses()) {
            $this->query->addWhereClause(new WhereClause($column, $operator, $value));

            return $this;
        }

        $this->query->addWhereClause(new WhereClause($column, $operator, $value, $connector));

        return $this;
    }

    public function whereNull(string $column, Connector $connector = Connector::And): static
    {
        return $this->where($column, 'IS', null, $connector);
    }

    public function whereNotNull(string $column, Connector $connector = Connector::And): static
    {
        return $this->where($column, 'IS NOT', null, $connector);
    }

    public function whereIn(string $column, array $values, Connector $connector = Connector::And): static
    {
        return $this->where($column, 'IN', $values, $connector);
    }

    public function whereNotIn(string $column, array $values, Connector $connector = Connector::And): static
    {
        return $this->where($column, 'NOT IN', $values, $connector);
    }

    /**
     * @return object[]
     *
     * @throws DatabaseException
     * @throws PDOException
     */
    public function get(): array
    {
        $this->query->setAction(Action::Select);
        $this->execute();

        return $this->fetch();
    }

    /**
     * @throws DatabaseException
     * @throws PDOException
     */
    public function first(): ?object
    {
        $this->query->setAction(Action::Select);
        $this->execute();

        return $this->fetch(true);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws DatabaseException
     * @throws PDOException
     */
    public function create(array $data): int
    {
        $this->query->setAction(Action::Insert);
        $this->query->setManipulationData($data);
        $this->execute();

        return (int) $this->handler->lastInsertId();
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws DatabaseException
     * @throws PDOException
     */
    public function update(array $data): int
    {
        $this->query->setAction(Action::Update);
        $this->query->setManipulationData($data);
        $this->execute();

        return $this->statement->rowCount();
    }

    /**
     * @throws DatabaseException
     * @throws PDOException
     */
    public function delete(): int
    {
        $this->query->setAction(Action::Delete);
        $this->execute();

        return $this->statement->rowCount();
    }

    protected function dataSourceName(): string
    {
        return "mysql:host={$this->host};dbname={$this->name};charset={$this->charset}";
    }

    protected function handlerOptions(): array
    {
        return [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ];
    }

    /**
     * @throws DatabaseException
     * @throws PDOException
     */
    protected function execute(): void
    {
        if (! $this->handler instanceof PDO) {
            throw DatabaseException::handlerNotSet();
        }

        $this->statement = $this->handler->prepare($this->query->build());
        $this->statement->execute($this->query->getBindings());
    }

    /**
     * @return null|object|object[]
     *
     * @throws DatabaseException
     * @throws PDOException
     */
    protected function fetch(bool $single = false): null|object|array
    {
        if (! $this->statement instanceof PDOStatement) {
            throw DatabaseException::statementNotSet();
        }

        if ($single) {
            $result = $this->statement->fetch();

            return $result === false ? null : $result;
        }

        $results = $this->statement->fetchAll();

        return $results === false ? [] : $results;
    }
}
