<?php

use Digitaliseme\Core\Application;

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
