<?php

namespace Digitaliseme\Core\Session;

use Digitaliseme\Core\Enumerations\Key;

class CSRF
{
    private static ?self $instance = null;
    private string $key;

    final private function __construct()
    {
        $this->key = Key::CsrfToken->value;
    }

    public static function handler(): self
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function token(): ?string
    {
        return $_SESSION[$this->key] ?? null;
    }

    public function generateToken(bool $force = false): void
    {
        $mustGenerate = $force || $this->missing();

        if ($mustGenerate) {
            $_SESSION[$this->key] = hash('sha256', randomString());
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
        unset($_SESSION[$this->key]);
    }
}
