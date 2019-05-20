<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= SITE_INFO['name'].' | '.$data['title']; ?></title>
    <link rel="stylesheet" href="<?= HOME.'app/public/css/main.css'; ?>">
</head>
<body>
    <header>
        <div id="logo">
            <img src="<?= HOME.'app/public/img/logo.png'; ?>">
        </div>
        <div id="site_title">
            <h1><?= SITE_INFO['name']; ?></h1>
            <h2><?= SITE_INFO['description']; ?></h2>
        </div>
        <div id="user_info">
            <p>
                <?= Core\Helper::setGreeting(); ?>
            </p>
        </div>
    </header>
    <main>