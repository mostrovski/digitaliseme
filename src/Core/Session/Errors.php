<?php

namespace Digitaliseme\Core\Session;

class Errors
{
    private static ?self $instance = null;
    final private function __construct() {}

    public static function handler(): self
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * @param array<string,array<int,string>> $errors
     */
    public function set(array $errors): void
    {
        $_SESSION['errors'] = $errors;
    }

    public function get(?string $key = null, bool $allPerKey = false): array|string|null
    {
        $errors = $_SESSION['errors'] ?? null;

        if (empty($errors) || ! is_array($errors)) {
            return null;
        }

        if (empty($key)) {
            return $errors;
        }

        if (array_key_exists($key, $errors)) {
            return $allPerKey ? $errors[$key] : current($errors[$key]);
        }

        return null;
    }

    public function clear(): void
    {
        unset($_SESSION['errors']);
    }
}
