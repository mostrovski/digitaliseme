<?php

require __DIR__.'/../vendor/autoload.php';

session_start();

$app = Digitaliseme\Core\Application::resolve();
$app->start();
$http = Digitaliseme\Core\Http\Http::handler($app);
$http->handleRequest()->sendResponse();
$app->terminate();
