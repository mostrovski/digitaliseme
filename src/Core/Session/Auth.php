<?php

namespace Digitaliseme\Core\Session;

use Digitaliseme\Core\Contracts\Authenticatable;
use Digitaliseme\Core\Enumerations\Key;

class Auth
{
    private static ?self $instance = null;
    private string $key;

    final private function __construct()
    {
        $this->key = Key::Authenticated->value;
    }

    public static function handler(): self
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function persist(Authenticatable $user): void
    {
        if ($this->isIntact()) {
            return;
        }
        $_SESSION[$this->key] = $user;
        CSRF::handler()->generateToken(force: true);
    }

    public function user(): ?Authenticatable
    {
        return $_SESSION[$this->key] ?? null;
    }

    public function id(): int|string|null
    {
        return $this->user()?->authIdentifier();
    }

    public function isIntact(): bool
    {
        return $this->user() instanceof Authenticatable;
    }

    public function isMissing(): bool
    {
        return ! $this->isIntact();
    }

    public function clear(): void
    {
        unset($_SESSION[$this->key]);
        CSRF::handler()->clear();
    }
}
