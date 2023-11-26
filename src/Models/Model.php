<?php

namespace Digitaliseme\Models;

use Digitaliseme\Core\Database\DB;
use Digitaliseme\Core\Database\MySQL;
use Digitaliseme\Core\Database\Query;

abstract class Model
{
    protected DB $db;
    protected ?string $table = null;

    public function __construct()
    {
        $this->db = DB::wire(MySQL::connect(), new Query)
            ->setFetchClass(static::class)
            ->table($this->getTableName());
    }

    public function query(): DB
    {
        return $this->db;
    }
    public function getTableName(): string
    {
        return empty($this->table) ? $this->guessTableName() : $this->table;
    }

    protected function guessTableName(): string
    {
        $modelNamePartials = explode('\\', static::class);
        $modelName = end($modelNamePartials);
        $tableName = '';
        $length = strlen($modelName);

        for ($i = 0; $i < $length; $i++) {
            $char = $modelName[$i];
            if ($i !== 0 && ctype_upper($char)) {
                $tableName .= '_';
            }
            $tableName .= strtolower($char);
        }

        return rtrim($tableName, 's').'s';
    }

    /**
     * @param array<string,mixed> $params
     */
    abstract public function create(array $params): static;
}
