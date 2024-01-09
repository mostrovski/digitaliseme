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

    'regex' => [
        'email_match' => '/^[a-zA-Z0-9.!$%&*+\/\=^_{\|}~-]{3,}@[a-zA-Z0-9-]{3,}(\.[a-zA-Z]{2,})$/',
        'email_san' => '/[^a-zA-Z0-9.@!$%&*+\/\=^_{\|}~-]/',
        'keywords_match' => '/^\s*([^,\s-]{2,}\s?[^,\s-]*){1,}\s*,\s*([^\s-]{2,}\s?[^\s-]*)*\s*$/',
        'keywords_san' => '/[^a-zA-ZäöüßÄÖÜ0-9,\s-]/',
        'name' => '/[^a-zA-ZäöüßÄÖÜ-]/',
        'user_name' => '/[^a-zA-Z0-9_-]/',
        'file_name' => '/[^a-zA-ZäöüßÄÖÜ0-9_-]/',
        'agent_name' => '/[^a-zA-ZäöüßÄÖÜ0-9.\s-]/',
        'doc_title' => '/[^a-zA-ZäöüßÄÖÜ0-9()*,.\s-]/',
        'phone' => '/[^0-9+()-]/',
        'storage_name' => '/[^a-zA-ZäöüßÄÖÜ0-9,()\s-]/',
    ],

    'messages' => [
        'error' => [
            'GENERAL_ERROR' => 'something went wrong...',
        ],
    ],
];
