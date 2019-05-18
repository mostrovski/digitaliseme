<?php
require_once 'config/config.php';
require_once 'config/autoload.php';
session_start();
Core\Page::render();
?>