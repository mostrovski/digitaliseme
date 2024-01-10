<?php

require __DIR__.'/../vendor/autoload.php';

session_start();

$app = Digitaliseme\Core\Application::resolve();
$app->start();
$http = new Digitaliseme\Core\Http\Http($app);
$http->handleRequest()->sendResponse();
$app->terminate();
