<?php

namespace Digitaliseme\Core\Database;

use Digitaliseme\Core\Database\Contracts\Connection;
use Digitaliseme\Core\Database\Contracts\SqlBuilder;
use Digitaliseme\Exceptions\DatabaseException;
use PDO;
use PDOException;
use PDOStatement;

// TODO: handle Exceptions
class DB
{
    protected PDO $handler;
    protected SqlBuilder $query;

    protected ?string $fetchClass = null;
    protected mixed $statement = null;

    final public function __construct(Connection $connection, SqlBuilder $query) {
        $this->handler = $connection->handler();
        $this->query = $query;
    }

    public static function wire(Connection $connection, SqlBuilder $query): static
    {
        return new static($connection, $query);
    }

    public function setFetchClass(string $className): static
    {
        if (class_exists($className)) {
            $this->fetchClass = $className;
        }

        return $this;
    }

    public function table(string $name): static
    {
        $this->query->useTable($name);

        return $this;
    }

    public function select(string ...$columns): static
    {
        $this->query->useSelected($columns);

        return $this;
    }

    public function where(string $column, string $operator, mixed $value, WhereGlue $glue = WhereGlue::And): static
    {
        $this->query->useWhereClause($column, $operator, $value, $glue);

        return $this;
    }

    public function whereNull(string $column, WhereGlue $glue = WhereGlue::And): static
    {
        return $this->where($column, 'IS', null, $glue);
    }

    public function whereNotNull(string $column, WhereGlue $glue = WhereGlue::And): static
    {
        return $this->where($column, 'IS NOT', null, $glue);
    }

    public function whereIn(string $column, array $values, WhereGlue $glue = WhereGlue::And): static
    {
        return $this->where($column, 'IN', $values, $glue);
    }

    public function whereNotIn(string $column, array $values, WhereGlue $glue = WhereGlue::And): static
    {
        return $this->where($column, 'NOT IN', $values, $glue);
    }

    /**
     * @return object[]
     *
     * @throws DatabaseException
     * @throws PDOException
     */
    public function get(): array
    {
        $this->query->useAction(Action::Select);
        $this->execute();

        return $this->fetchAll();
    }

    /**
     * @throws DatabaseException
     * @throws PDOException
     */
    public function first(): ?object
    {
        $this->query->useAction(Action::Select);
        $this->execute();

        return $this->fetchOne();
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws DatabaseException
     * @throws PDOException
     */
    public function create(array $data): int
    {
        $this->query->useAction(Action::Insert);
        $this->query->useManipulationData($data);
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
        $this->query->useAction(Action::Update);
        $this->query->useManipulationData($data);
        $this->execute();

        return $this->statement->rowCount();
    }

    /**
     * @throws DatabaseException
     * @throws PDOException
     */
    public function delete(): int
    {
        $this->query->useAction(Action::Delete);
        $this->execute();

        return $this->statement->rowCount();
    }

    /**
     * @throws DatabaseException
     * @throws PDOException
     */
    protected function execute(): void
    {
        $this->statement = $this->handler->prepare($this->query->toSql());

        if (! $this->statement instanceof PDOStatement) {
            throw DatabaseException::sqlPreparationFailed();
        }

        if (! empty($this->fetchClass)) {
            $this->statement->setFetchMode(PDO::FETCH_CLASS, $this->fetchClass);
        }

        $this->statement->execute($this->query->bindings());

        $this->query->reset();
    }

    /**
     * @throws DatabaseException
     * @throws PDOException
     */
    protected function fetchOne(): ?object
    {
        if (! $this->statement instanceof PDOStatement) {
            throw DatabaseException::statementNotSet();
        }

        $result = $this->statement->fetch();

        return $result === false ? null : $result;
    }

    /**
     * @throws DatabaseException
     * @throws PDOException
     */
    protected function fetchAll(): array
    {
        if (! $this->statement instanceof PDOStatement) {
            throw DatabaseException::statementNotSet();
        }

        $results = $this->statement->fetchAll();

        return $results === false ? [] : $results;
    }
}
