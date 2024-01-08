<?php

namespace Digitaliseme\Core\Session;

class RedirectData
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

    public function set(array $data): void
    {
        $_SESSION['redirect-data'] = $data;
    }

    public function get(): array
    {
        return $_SESSION['redirect-data'] ?? [];
    }

    public function clear(): void
    {
        unset($_SESSION['redirect-data']);
    }
}
