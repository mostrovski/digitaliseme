<?php

require __DIR__.'/../vendor/autoload.php';

session_start();

$app = Digitaliseme\Core\Application::resolve();
$app->start();
$response = Digitaliseme\Core\Page::render(); // TODO
$response instanceof \Digitaliseme\Core\Contracts\Response && $response->send(); // TODO
$app->terminate();
