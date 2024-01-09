<?php

require __DIR__.'/../vendor/autoload.php';

session_start();

$app = Digitaliseme\Core\Application::resolve();
$app->start();
$response = $app->handleRequest();
$response->send();
$app->terminate();
