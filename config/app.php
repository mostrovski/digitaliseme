<?php

return [
    'db' => [
        'host' => 'db',
        'name' => 'db',
        'user' => 'db',
        'password' => 'db',
        'charset' => 'utf8mb4',
    ],

    'url' => 'https://digitaliseme.ddev.site/',

    'info' => [
        'name' => 'digitalise me',
        'description' => 'cozy document archive',
        'developer' => 'Andrei Ostrovskii',
    ],

    'files' => [
        'supported_types' => ['application/pdf', 'image/jpeg', 'image/png'],
        'max_size' => 1048576,
    ],

    'messages' => [
        'error' => [
            'GENERAL_ERROR' => 'something went wrong...',
        ],
    ],
];
