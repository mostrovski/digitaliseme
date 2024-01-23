<?php

namespace Digitaliseme\Core\ORM;

use Digitaliseme\Core\Database\DB;
use Digitaliseme\Core\Database\MySQL;
use Digitaliseme\Core\Database\Query;
use Digitaliseme\Core\Exceptions\DatabaseException;
use Digitaliseme\Core\Exceptions\RecordNotFoundException;
use Digitaliseme\Core\ORM\Meta\ModelAttribute;
use Digitaliseme\Core\ORM\Meta\Setter;
use Digitaliseme\Core\Traits\Reflectable;
use PDOException;
use ReflectionAttribute;
use ReflectionProperty;

abstract class Model
{
    use Reflectable;

    protected DB $db;
    protected ?string $table = null;
    protected string $primaryKey = 'id';
    /**
     * @var array<int,string>
     */
    protected array $attributes = [];
    /**
     * @var array<int,string>
     */
    protected array $protectedOnCreate = [];
    /**
     * @var array<int,string>
     */
    protected array $protectedOnUpdate = [];
    /**
     * @var array<string,string>
     */
    protected array $setters = [];

    public function __construct()
    {
        $this->db = DB::wire(MySQL::connect(), new Query)
            ->setFetchClass(static::class)
            ->table($this->getTableName());

        $this->registerAttributes();
        $this->registerSetters();
    }

    public static function go(): static
    {
        return new static;
    }

    public function query(): DB
    {
        return $this->db;
    }

    public function pivot(string $table): DB
    {
        return DB::wire(MySQL::connect(), new Query)->table($table);
    }

    /**
     * @param array<string,mixed> $params
     *
     * @throws DatabaseException
     */
    public function create(array $params): static
    {
        $params = $this->filteredParams($params, $this->protectedOnCreate);

        try {
            $id = $this->db->create($params);
            $newInstance = $this->db->where($this->primaryKey, '=', $id)->first();
        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }

        if (! $newInstance instanceof static) {
            throw new DatabaseException('Could not create new instance.');
        }

        return $newInstance;
    }

    /**
     * @param array<string,mixed> $params
     *
     * @throws DatabaseException
     */
    public function update(array $params): void
    {
        $params = $this->filteredParams($params, $this->protectedOnUpdate);

        try {
            $this->db
                ->where($this->primaryKey, '=', $this->{$this->primaryKey})
                ->update($params);
        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }

    /**
     * @param array<string,mixed> $params
     *
     * @throws DatabaseException
     */
    public function firstOrCreate(array $params, ?string $uniqueKey = null): static
    {
        if (empty($uniqueKey)) {
            /** @var array<int,string> $keys */
            $keys = array_keys($params);

            if (count($keys) > 1) {
                throw new DatabaseException('A unique key is missing.');
            }

            $uniqueKey = $keys[0];
        }

        if (! isset($params[$uniqueKey])) {
            throw new DatabaseException('A unique key is missing.');
        }

        try {
            return $this->db->where($uniqueKey, '=', $params[$uniqueKey])
                ->firstOrFail();
        } catch (RecordNotFoundException) {
            return $this->create($params);
        }
    }

    /**
     * @param array<string,mixed> $params
     *
     * @throws DatabaseException
     */
    public function updateOrCreate(array $params, string $uniqueKey): static
    {
        if (empty($uniqueKey) || ! isset($params[$uniqueKey])) {
            throw new DatabaseException('A unique key is missing.');
        }

        try {
            $instance = $this->db->where($uniqueKey, '=', $params[$uniqueKey])
                ->firstOrFail();

            $instance->update($params);

            return $instance;
        } catch (RecordNotFoundException) {
            return $this->create($params);
        }
    }

    /**
     * @throws DatabaseException
     */
    public function find(mixed $primaryKey): ?static
    {
        try {
            $model = $this->db->where($this->primaryKey, '=', $primaryKey)->first();
        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }

        if (! $model instanceof static) {
            return null;
        }

        return $model;
    }

    /**
     * @throws DatabaseException
     * @throws RecordNotFoundException
     */
    public function findOrFail(mixed $primaryKey): static
    {
        try {
            $model = $this->db->where($this->primaryKey, '=', $primaryKey)->firstOrFail();
        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }

        return $model;
    }

    /**
     * @throws DatabaseException
     */
    public function delete(): bool
    {
        if (! isset($this->{$this->primaryKey})) {
            return false;
        }

        return (bool) $this->db
            ->where($this->primaryKey, '=', $this->{$this->primaryKey})
            ->delete();
    }

    public function getTableName(): string
    {
        return empty($this->table) ? $this->guessTableName() : $this->table;
    }

    protected function registerSetters(): void
    {
        $attributes = $this->getAttributeProperties();

        if (empty($attributes)) {
            return;
        }

        foreach ($attributes as $property) {
            $reflectionAttribute = current($property->getAttributes(Setter::class));

            if (! $reflectionAttribute instanceof ReflectionAttribute) {
                continue;
            }
            /** @var Setter $setter */
            $setter = $reflectionAttribute->newInstance();

            if ($this->reflection()->hasMethod($setter->methodName)) {
                $this->setters[$property->getName()] = $setter->methodName;
            }
        }
    }

    protected function registerAttributes(): void
    {
        $properties = $this->getAttributeProperties();

        if (empty($properties)) {
            return;
        }

        foreach ($properties as $property) {
            /** @var ReflectionAttribute $attribute */
            $attribute = current($property->getAttributes(ModelAttribute::class));
            /** @var ModelAttribute $modelAttribute */
            $modelAttribute = $attribute->newInstance();

            if ($modelAttribute->protectedOnCreate) {
                $this->protectedOnCreate[] = $property->getName();
            }

            if ($modelAttribute->protectedOnUpdate) {
                $this->protectedOnUpdate[] = $property->getName();
            }

            $this->attributes[] = $property->getName();
        }
    }

    /**
     * @return ReflectionProperty[]
     */
    protected function getAttributeProperties(): array
    {
        $properties = $this->reflection()->getProperties();

        return array_filter($properties, static fn ($property) => (
            ! empty($property->getAttributes(ModelAttribute::class))
        ));
    }

    /**
     * @param array<string,mixed> $params
     *
     * @return array<string,mixed>
     */
    protected function filteredParams(array $params, array $protected): array
    {
        $filtered = [];

        foreach ($params as $key => $value) {
            if (in_array($key, $protected, true)) {
                continue;
            }

            if (! in_array($key, $this->attributes, true)) {
                continue;
            }

            if (array_key_exists($key, $this->setters)) {
                $method = $this->setters[$key];
                $filtered[$key] = $this->{$method}($value);
                continue;
            }

            $filtered[$key] = $value;
        }

        return $filtered;
    }

    protected function guessTableName(): string
    {
        $modelast_name = basename(str_replace('\\', '/', static::class));
        $tableName = '';
        $length = strlen($modelast_name);

        for ($i = 0; $i < $length; ++$i) {
            $char = $modelast_name[$i];

            if ($i !== 0 && ctype_upper($char)) {
                $tableName .= '_';
            }
            $tableName .= strtolower($char);
        }

        return rtrim($tableName, 's').'s';
    }

    public function __serialize(): array
    {
        $serialized = [];

        foreach ($this->attributes as $attribute) {
            $serialized[$attribute] = $this->{$attribute};
        }

        return $serialized;
    }
}
