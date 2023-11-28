<?php

namespace Digitaliseme\Core\Validation;

use Digitaliseme\Core\Exceptions\RuleException;
use Digitaliseme\Core\Exceptions\ValidatorException;

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
                    $this->applyRule($value, $rule);
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

    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @throws RuleException
     * @throws ValidatorException
     */
    protected function applyRule(mixed $value, string $rule): void
    {
        $ruleParts = explode(':', $rule);
        $ruleName = $ruleParts[0];
        $ruleArgs = $ruleParts[1] ?? null;

        if (! method_exists($this, $ruleName)) {
            throw new ValidatorException('Rule '.$ruleName.' does not exist.');
        }

        $passes = $this->{$ruleName}($value, $ruleArgs);

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
}
