<?php

namespace Digitaliseme\Core\Validation;

use Digitaliseme\Core\Database\DB;
use Digitaliseme\Core\Database\MySQL;
use Digitaliseme\Core\Database\Query;
use Digitaliseme\Core\Exceptions\RecordNotFoundException;
use Digitaliseme\Core\Exceptions\RuleException;
use Digitaliseme\Core\Exceptions\ValidatorException;
use Throwable;

class Validator
{
    /** @var array<string,mixed> */
    protected array $validated = [];
    /** @var array<string,array<int,string>> */
    protected array $errors = [];

    public function __construct(
        /** @var array<string,mixed> */
        protected array $params,
        /** @var array<string,array<int,string>> */
        protected array $rules,
        /** @var array<string,string> */
        protected array $messages,
    ) {}

    /**
     * @throws ValidatorException
     */
    public function validate(): Validator
    {
        foreach ($this->params as $key => $value) {
            if (! array_key_exists($key, $this->rules)) {
                $this->validated[$key] = $value;
                continue;
            }

            foreach ($this->rules[$key] as $rule) {
                try {
                    $this->applyRule($value, $rule, $key);
                    $this->validated[$key] = $value;
                } catch (RuleException) {
                    $this->errors[$key][] = $this->parseMessage($key, $rule);
                }
            }
        }

        return $this;
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function fails(): bool
    {
        return ! $this->passes();
    }

    public function getValidated(): array
    {
        return $this->validated;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @throws RuleException
     * @throws ValidatorException
     */
    protected function applyRule(mixed $value, string $rule, string $key): void
    {
        $ruleParts = explode(':', $rule);
        $ruleName = $ruleParts[0];
        $ruleArgs = $ruleParts[1] ?? null;

        if (! method_exists($this, $ruleName)) {
            throw new ValidatorException('Rule '.$ruleName.' does not exist.');
        }

        $passes = $this->{$ruleName}($value, $ruleArgs, $key);

        if (! $passes) {
            throw new RuleException;
        }
    }

    protected function parseMessage(string $key, string $rule): string
    {
        $message = $this->messages[$key.'.'.current(explode(':', $rule))] ?? null;

        if (! $message) {
            $message = 'The field '.$key.' is invalid.';
        }

        return $message;
    }

    protected function required(mixed $value): bool
    {
        return ! empty($value);
    }

    protected function min(mixed $value, string $argument): bool
    {
        $required = (int) $argument;

        if (is_numeric($value)) {
            return $value >= $required;
        }

        if (is_string($value)) {
            return mb_strlen($value) >= $required;
        }

        if (is_array($value)) {
            return count($value) >= $required;
        }

        return false;
    }

    protected function max(mixed $value, string $argument): bool
    {
        $required = (int) $argument;

        if (is_numeric($value)) {
            return $value <= $required;
        }

        if (is_string($value)) {
            return mb_strlen($value) <= $required;
        }

        if (is_array($value)) {
            return count($value) <= $required;
        }

        return false;
    }

    protected function regex(string $value, string $pattern): bool
    {
        return preg_match($pattern, $value) === 1;
    }

    protected function email(string $value): bool
    {
        return $this->regex(
            $value,
            '/^[a-zA-Z0-9.!$%&*+\/\=^_{\|}~-]{3,}@[a-zA-Z0-9-]{3,}(\.[a-zA-Z]{2,})$/',
        );
    }

    protected function unique(mixed $value, string $table, string $column): bool
    {
        try {
            DB::wire(MySQL::connect(), new Query)
                ->table($table)
                ->where($column, '=', $value)
                ->firstOrFail();

            return false;
        } catch (RecordNotFoundException) {
            return true;
        } catch (Throwable) {
            // Log error
            return false;
        }
    }
}
