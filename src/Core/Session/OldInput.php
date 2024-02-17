<?php

namespace Digitaliseme\Core\Session;

use Digitaliseme\Core\Enumerations\Key;
use Digitaliseme\Core\Http\Request;

class OldInput
{
    private static ?self $instance = null;
    private string $key;

    final private function __construct()
    {
        $this->key = Key::OldInput->value;
    }

    public static function handler(): self
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function set(): void
    {
        if ($this->exists()) {
            return;
        }

        $_SESSION[$this->key] = Request::resolve()->data();
    }

    public function get(string $key): mixed
    {
        if (! $this->exists()) {
            return null;
        }

        return $_SESSION[$this->key][$key] ?? null;
    }

    public function clear(): void
    {
        unset($_SESSION[$this->key]);
    }

    protected function exists(): bool
    {
        $old = $_SESSION[$this->key] ?? null;

        if (empty($old)) {
            return false;
        }

        if (! is_array($old)) {
            return false;
        }

        return true;
    }
}
