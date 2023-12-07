<?php

use Digitaliseme\Core\Application;
use Digitaliseme\Core\Messaging\Flash;
use Digitaliseme\Core\Messaging\OldInput;

function app(): Application
{
    return Application::resolve();
}

function config(string $key, $default = null): mixed
{
    $config = app()->config();

    if (array_key_exists($key, $config)) {
        return $config[$key];
    }

    if (str_contains($key, '.')) {
        $stairs = explode('.', $key);
        $ladder = $config;

        foreach ($stairs as $stair) {
            if (is_array($ladder) && array_key_exists($stair, $ladder)) {
                $ladder = $ladder[$stair];
                continue;
            }

            return $default;
        }

        return $ladder;
    }

    return $default;
}

function errors(?string $key = null, bool $allPerKey = false): array|string|null
{
    $errors = $_SESSION['errors'] ?? null;

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

function clearErrors(): void
{
    unset($_SESSION['errors']);
}

function flash(): Flash
{
    return new Flash;
}

function clearFlash(): void
{
    flash()->clear();
}

/**
 * @return OldInput|mixed
 */
function old(?string $key = null): mixed
{
    $old = new OldInput;

    if (empty($key)) {
        return $old;
    }

    return $old->get($key);
}

function clearOld(): void
{
    (new OldInput)->clear();
}
