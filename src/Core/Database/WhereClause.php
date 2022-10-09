<?php

namespace Digitaliseme\Core\Database;

class WhereClause
{
    public function __construct(
        protected string $column,
        protected string $operator,
        protected mixed $value,
        protected ?WhereGlue $glue = null,
    ) {}

    public function sql(): string
    {
        return "{$this->where()} {$this->column} {$this->operator} {$this->placeholder()} ";
    }

    public function bind(): mixed
    {
        if (is_array($this->value)) {
            return array_values($this->value);
        }

        return $this->value;
    }

    protected function where(): string
    {
        return $this->glue instanceof WhereGlue ? $this->glue->value : 'WHERE';
    }

    protected function placeholder(): string
    {
        if (in_array(strtoupper($this->operator), ['IN', 'NOT IN'], true)) {
            $number = is_array($this->value) ? count($this->value) : 1;

            return '('.rtrim(str_repeat('?, ', $number), ', ').')';
        }

        return '?';
    }
}
