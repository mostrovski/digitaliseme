<?php

namespace Digitaliseme\Core\Contracts;

use Digitaliseme\Core\Enumerations\Database\Action;
use Digitaliseme\Core\Enumerations\Database\WhereGlue;
use Digitaliseme\Core\Exceptions\DatabaseException;

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

    public function reset();
}
