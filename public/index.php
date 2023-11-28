<?php

require __DIR__.'/../vendor/autoload.php';

session_start();

Digitaliseme\Core\Application::resolve();
Digitaliseme\Core\Page::render();

clearErrors();
clearFlash();
