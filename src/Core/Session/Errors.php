<?php

namespace Digitaliseme\Core\Session;

use Digitaliseme\Core\Enumerations\Key;

class Errors
{
    private static ?self $instance = null;
    private string $key;

    final private function __construct()
    {
        $this->key = Key::Errors->value;
    }

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
        $_SESSION[$this->key] = $errors;
    }

    public function get(?string $key = null, bool $allPerKey = false): null|array|string
    {
        $errors = $_SESSION[$this->key] ?? null;

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
        unset($_SESSION[$this->key]);
    }
}
