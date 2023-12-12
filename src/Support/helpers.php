<?php

use Digitaliseme\Core\Application;
use Digitaliseme\Core\Logging\Logger;
use Digitaliseme\Core\Session\Errors;
use Digitaliseme\Core\Session\Flash;
use Digitaliseme\Core\Session\OldInput;

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

function storage_path(?string $relativePath = null, bool $public = false): string
{
    $path = app()->root().'/storage';

    if ($public) {
        $path .= '/public';
    }

    if (empty($relativePath)) {
        return $path;
    }

    return $path.'/'.ltrim($relativePath, '/');
}

function documents_path(?string $relativePath = null): string
{
    return storage_path(rtrim('documents/'.$relativePath, '/'), public: true);
}

function logs_path(?string $relativePath = null): string
{
    return storage_path(rtrim('logs/'.$relativePath, '/'));
}

function logger(): Logger
{
    return new Logger;
}

function errors(?string $key = null, bool $allPerKey = false): Errors|array|string|null
{
    $errors = Errors::handler();

    if (empty($key)) {
        return $errors;
    }

    return $errors->get($key, $allPerKey);
}

function flash(): Flash
{
    return Flash::handler();
}

/**
 * @return OldInput|mixed
 */
function old(?string $key = null): mixed
{
    $old = OldInput::handler();

    if (empty($key)) {
        return $old;
    }

    return $old->get($key);
}

function show(mixed $input): string
{
    return htmlspecialchars((string) $input);
}

function randomString(string $prefix = ''): string
{
    return str_replace('.', '', uniqid($prefix, true));
}

function dump(...$values): void
{
    echo '<pre>';
    foreach ($values as $value) {
        var_dump($value);
    }
    echo '</pre>';
    exit;
}
