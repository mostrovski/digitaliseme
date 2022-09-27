<?php

require __DIR__.'/../config/config.php';
require __DIR__.'/../vendor/autoload.php';

session_start();

Digitaliseme\Core\Page::render();
