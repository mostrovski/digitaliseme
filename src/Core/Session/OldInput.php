<?php

namespace Digitaliseme\Core\Session;

use Digitaliseme\Core\Http\Request;

class OldInput
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

    public function set(): void
    {
        if ($this->exists()) {
            return;
        }

        $_SESSION['old'] = Request::resolve()->data();
    }

    public function get(string $key): mixed
    {
        if (! $this->exists()) {
            return null;
        }

        return $_SESSION['old'][$key] ?? null;
    }

    public function clear(): void
    {
        unset($_SESSION['old']);
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
