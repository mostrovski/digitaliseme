<?php

require __DIR__.'/../vendor/autoload.php';

session_start();

Digitaliseme\Core\Application::resolve();
Digitaliseme\Core\Page::render();

echo '<pre>';
var_dump((new \Digitaliseme\Models\User)->create([
    'id' => 777,
    'uname' => 'test2',
    'fname' => 'test2',
    'lname' => 'test2',
    'email' => 'test2@test.com',
    'password' => 'test2',
]));
echo '</pre>';
