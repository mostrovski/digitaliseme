<?php

namespace Digitaliseme\Core\Database;

class InsertClause
{
    /** @var array<string, mixed> */
    protected array $data = [];

    public function sql(): string
    {
        $columns = array_keys($this->data);

        return '('.implode(',', $columns).') VALUES ('.str_repeat('?,', count($columns)).')';
    }

    public function bind(): array
    {
        return array_values($this->data);
    }
}
