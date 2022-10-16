<?php

namespace Digitaliseme\Core\Database\Contracts;

use Digitaliseme\Core\Database\Action;
use Digitaliseme\Core\Database\WhereGlue;
use Digitaliseme\Exceptions\DatabaseException;

interface SqlBuilder
{
    /**
     * @throws DatabaseException
     */
    public function toSql(): string;

    public function bindings(): array;

    public function useTable(string $name);

    public function useAction(Action $type);

    public function useSelected(array $columns);

    public function useManipulationData(array $data);

    public function useWhereClause(string $column, string $operator, mixed $value, WhereGlue $glue);
}
