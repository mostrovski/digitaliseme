<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= SITE_INFO['name'].' | '.$data['title'] ?></title>
    <link rel="stylesheet" href="<?= HOME.'css/main.css' ?>">
</head>
<body>
    <header>
        <div id="logo">
            <img src="<?= HOME.'img/logo.png' ?>">
        </div>
        <div id="site_title">
            <h1><?= SITE_INFO['name'] ?></h1>
            <h2><?= SITE_INFO['description'] ?></h2>
        </div>
        <div id="user_info">
            <p>
                <?= Digitaliseme\Core\Helper::getGreeting() ?>
            </p>
        </div>
    </header>
    <main>
