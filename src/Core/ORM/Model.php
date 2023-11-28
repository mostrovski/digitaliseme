<?php

namespace Digitaliseme\Core\ORM;

use Digitaliseme\Core\Database\DB;
use Digitaliseme\Core\Database\MySQL;
use Digitaliseme\Core\Database\Query;
use Digitaliseme\Core\Exceptions\DatabaseException;
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

        $this->registerProtectedAttributes();
        $this->registerSetters();
    }

    public function query(): DB
    {
        return $this->db;
    }

    /**
     * @param array<string,mixed> $params
     * @throws DatabaseException
     */
    public function create(array $params): static
    {
        $params = $this->filteredParams($params, $this->protectedOnCreate);
        try {
            $id = $this->db->create($params);
            $newInstance = $this->db->where('id', '=', $id)->first();
        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }

        if (! $newInstance instanceof static) {
            throw new DatabaseException('Could not create new instance.');
        }

        return $newInstance;
    }
    public function getTableName(): string
    {
        return empty($this->table) ? $this->guessTableName() : $this->table;
    }
    protected function registerSetters(): void
    {
        $attributes = $this->getAttributes();

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

    protected function registerProtectedAttributes(): void
    {
        $attributes = $this->getAttributes();

        if (empty($attributes)) {
            return;
        }

        foreach ($attributes as $property) {
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
        }
    }

    /**
     * @return ReflectionProperty[]
     */
    protected function getAttributes(): array
    {
        $properties = $this->reflection()->getProperties();

        return array_filter($properties, static fn($property) => (
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

            if (array_key_exists($key, $this->setters)) {
                $method = $this->setters[$key];
                $filtered[$key] = $this->$method($value);
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

        for ($i = 0; $i < $length; $i++) {
            $char = $modelast_name[$i];
            if ($i !== 0 && ctype_upper($char)) {
                $tableName .= '_';
            }
            $tableName .= strtolower($char);
        }

        return rtrim($tableName, 's').'s';
    }
}
