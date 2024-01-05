<?php

namespace Digitaliseme\Core\Session;

use Digitaliseme\Core\Contracts\Authenticatable;

class Auth
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

    public function persist(Authenticatable $user): void
    {
        if ($this->isIntact()) {
            return;
        }
        $_SESSION['authenticated'] = $user;
        CSRF::handler()->generateToken(force: true);
    }

    public function user(): ?Authenticatable
    {
        return $_SESSION['authenticated'] ?? null;
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
        unset($_SESSION['authenticated']);
        CSRF::handler()->clear();
    }
}
