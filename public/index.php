<?php

require __DIR__.'/../vendor/autoload.php';

session_start();

$app = Digitaliseme\Core\Application::resolve();
$app->start();
Digitaliseme\Core\Page::render();
$app->terminate();
