<?php

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../config/config.php';

session_start();

Digitaliseme\Core\Page::render();
