<?php

namespace Digitaliseme\Core\Session;

use Digitaliseme\Core\Enumerations\Key;

class RedirectData
{
    private static ?self $instance = null;
    private string $key;

    final private function __construct()
    {
        $this->key = Key::RedirectData->value;
    }

    public static function handler(): self
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function set(array $data): void
    {
        $_SESSION[$this->key] = $data;
    }

    public function get(): array
    {
        return $_SESSION[$this->key] ?? [];
    }

    public function clear(): void
    {
        unset($_SESSION[$this->key]);
    }
}
