<?php

use Digitaliseme\Core\Application;
use Digitaliseme\Core\Enumerations\Key;
use Digitaliseme\Core\Http\Request;
use Digitaliseme\Core\Http\Responses\Redirect;
use Digitaliseme\Core\Logging\Logger;
use Digitaliseme\Core\Session\Auth;
use Digitaliseme\Core\Session\CSRF;
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

function url(?string $relative = null): string
{
    $root = trim(config('app.url'), '/');

    if (empty($relative)) {
        return $root;
    }

    if (str_starts_with($relative, $root)) {
        return $relative;
    }

    return $root.'/'.ltrim($relative, '/');
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

function auth(): Auth
{
    return Auth::handler();
}

function csrf(): CSRF
{
    return CSRF::handler();
}

function show(mixed $input): string
{
    return htmlspecialchars((string) $input);
}

function randomString(string $prefix = ''): string
{
    return str_replace('.', '', uniqid($prefix, true));
}

function redirectResponse(string $target, array $data = []): Redirect
{
    $url = url($target);

    return (new Redirect($url))->with($data);
}

function request(): Request
{
    return Request::resolve();
}

function formToken(): string
{
    $name = Key::CsrfToken->value;
    $token = csrf()->token();

    return <<<HTML
        <input type="hidden" name="$name" value="$token">
    HTML;
}

function formAltMethod(string $method): string
{
    $name = Key::AltMethod->value;

    $requestMethod = match (strtoupper($method)) {
        'PUT', 'PATCH', 'DELETE' => $method,
        default => 'POST',
    };

    return <<<HTML
        <input type="hidden" name="$name" value="$requestMethod">
    HTML;
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
