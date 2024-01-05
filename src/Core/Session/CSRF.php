<?php

namespace Digitaliseme\Core\Session;

class CSRF
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

    public function token(): ?string
    {
        return $_SESSION['token'] ?? null;
    }

    public function generateToken(bool $force = false): void
    {
        $mustGenerate = $force || $this->missing();

        if ($mustGenerate) {
            $_SESSION['token'] = hash('sha256', randomString());
        }
    }

    public function exists(): bool
    {
        return ! empty($this->token());
    }

    public function missing(): bool
    {
        return ! $this->exists();
    }

    public function clear(): void
    {
        unset($_SESSION['token']);
    }
}
