<?php

namespace Digitaliseme\Core\Database;

use Digitaliseme\Core\Contracts\SqlBuilder;
use Digitaliseme\Core\Exceptions\DatabaseException;

class Query implements SqlBuilder
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
    public function toSql(): string
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

    public function bindings(): array
    {
        return $this->bindings;
    }

    public function useTable(string $name): void
    {
        $this->table = $name;
    }

    public function useAction(Action $type): void
    {
        $this->action = $type;
    }

    /**
     * @param string[] $columns
     */
    public function useSelected(array $columns): void
    {
        $this->selectables = $columns;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function useManipulationData(array $data): void
    {
        $this->manipulationData = $data;
    }

    public function useWhereClause(string $column, string $operator, mixed $value, WhereGlue $glue): void
    {
        if ($this->hasNoWhereClauses()) {
            $this->whereClauses[] = new WhereClause($column, $operator, $value);

            return;
        }

        $this->whereClauses[] = new WhereClause($column, $operator, $value, $glue);
    }

    public function reset(): void
    {
        $this->action = null;
        $this->bindings = [];
        $this->selectables = [];
        $this->manipulationData = [];
        $this->whereClauses = [];
    }

    protected function hasNoWhereClauses(): bool
    {
        return count($this->whereClauses) === 0;
    }

    protected function hasNoManipulationData(): bool
    {
        return count($this->manipulationData) === 0;
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
