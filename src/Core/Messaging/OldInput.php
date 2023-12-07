<?php

namespace Digitaliseme\Core\Messaging;

class OldInput
{
    public function clear(): void
    {
        unset($_SESSION['old']);
    }

    /**
     * @param string|array<string,mixed> $key
     */
    public function set(string|array $key, mixed $value = null): void
    {
        $_SESSION['old'] = is_string($key) ? [$key => $value] : $key;
    }

    public function get(string $key): mixed
    {
        if (! $this->exists()) {
            return null;
        }

        return $_SESSION['old'][$key] ?? null;
    }

    protected function exists(): bool
    {
        $old = $_SESSION['old'] ?? null;

        if (empty($old)) {
            return false;
        }

        if (! is_array($old)) {
            return false;
        }

        return true;
    }
}
