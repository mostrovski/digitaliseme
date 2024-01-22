<?php

namespace Digitaliseme\Core\Session;

use Digitaliseme\Core\Enumerations\Key;

class Flash
{
    private static ?self $instance = null;
    private string $key;

    final private function __construct()
    {
        $this->key = Key::Flash->value;
    }

    public static function handler(): self
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function success(string $message): void
    {
        $this->set($message, 'success');
    }

    public function error(string $message): void
    {
        $this->set($message, 'error');
    }

    public function warning(string $message): void
    {
        $this->set($message, 'warning');
    }

    public function info(string $message): void
    {
        $this->set($message, 'info');
    }

    public function clear(): void
    {
        unset($_SESSION[$this->key]);
    }

    public function getMessage(): ?string
    {
        $flash = $this->get();

        if (empty($flash)) {
            return null;
        }

        return $flash['message'];
    }

    public function getType(): ?string
    {
        $flash = $this->get();

        if (empty($flash)) {
            return null;
        }

        return $flash['type'];
    }

    protected function set(string $message, string $type): void
    {
        $_SESSION[$this->key] = ['message' => $message, 'type' => $type];
    }
    protected function get(): ?array
    {
        if (! $this->exists()) {
            return null;
        }

        return $_SESSION[$this->key];
    }

    protected function exists(): bool
    {
        $flash = $_SESSION[$this->key] ?? null;

        if (empty($flash)) {
            return false;
        }

        if (! is_array($flash)) {
            return false;
        }

        if (! array_key_exists('message', $flash) || ! array_key_exists('type', $flash)) {
            return false;
        }

        if (empty($flash['message']) || empty($flash['type'])) {
            return false;
        }

        return true;
    }
}
