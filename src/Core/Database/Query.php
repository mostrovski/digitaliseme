<?php

namespace Digitaliseme\Core\Database;

use Digitaliseme\Exceptions\DatabaseException;

class Query
{
    protected ?string $table = null;
    protected ?Action $action = null;
    protected array $bindings = [];

    /** @var string[] */
    protected array $selectables = [];
    /** @var array<string, mixed> */
    protected array $manipulationData = [];
    /** @var WhereClause[] */
    protected array $whereClauses = [];

    /**
     * @throws DatabaseException
     */
    public function build(): string
    {
        if (empty($this->table)) {
            throw DatabaseException::missingTable();
        }

        if ($this->action === null) {
            throw DatabaseException::missingAction();
        }

        if ($this->action === Action::Insert && $this->hasNoManipulationData()) {
            throw DatabaseException::missingInsertData();
        }

        if ($this->action === Action::Update && $this->hasNoManipulationData()) {
            throw DatabaseException::missingUpdateData();
        }

        return match ($this->action) {
            Action::Select => $this->buildSelect(),
            Action::Delete => $this->buildDelete(),
            Action::Insert => $this->buildInsert(),
            Action::Update => $this->buildUpdate(),
        };
    }

    public function getBindings(): array
    {
        return $this->bindings;
    }

    public function setTable(string $name): void
    {
        $this->table = $name;
    }

    public function setAction(Action $type): void
    {
        $this->action = $type;
    }

    /**
     * @param string[] $columns
     */
    public function setSelectables(array $columns): void
    {
        $this->selectables = $columns;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function setManipulationData(array $data): void
    {
        $this->manipulationData = $data;
    }

    public function hasNoManipulationData(): bool
    {
        return count($this->manipulationData) === 0;
    }

    public function addWhereClause(WhereClause $whereClause): void
    {
        $this->whereClauses[] = $whereClause;
    }

    public function hasNoWhereClauses(): bool
    {
        return count($this->whereClauses) === 0;
    }

    protected function buildSelect(): string
    {
        $columns = implode(', ', $this->selectables);

        if (empty($columns)) {
            $columns = '*';
        }

        return Action::Select->value." {$columns} FROM {$this->table} {$this->composeWhere()}";
    }

    protected function buildInsert(): string
    {
        $columns = implode(', ', array_keys($this->manipulationData));
        $placeholders = rtrim(str_repeat('?, ', count($this->manipulationData)), ', ');

        $this->bindings = array_merge(
            $this->bindings,
            array_values($this->manipulationData)
        );

        return Action::Insert->value." INTO {$this->table}({$columns}) VALUES({$placeholders})";
    }

    protected function buildUpdate(): string
    {
        $data = implode('=?, ', array_keys($this->manipulationData)).'=?';

        $this->bindings = array_merge(
            $this->bindings,
            array_values($this->manipulationData)
        );

        return Action::Update->value." {$this->table} SET {$data} {$this->composeWhere()}";
    }

    protected function buildDelete(): string
    {
        return Action::Delete->value." FROM {$this->table} {$this->composeWhere()}";
    }

    protected function composeWhere(): string
    {
        $where = '';

        foreach ($this->whereClauses as $clause) {
            $where .= $clause->sql();

            if (is_array($clause->bind())) {
                foreach ($clause->bind() as $value) {
                    $this->bindings[] = $value;
                }

                continue;
            }

            $this->bindings[] = $clause->bind();
        }

        return $where;
    }
}
