<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= config('app.info.name').' | '.$data['title'] ?></title>
    <link rel="stylesheet" href="<?= config('app.url').'css/main.css' ?>">
</head>
<body>
    <header>
        <div id="logo">
            <img src="<?= config('app.url').'img/logo.png' ?>">
        </div>
        <div id="site_title">
            <h1><?= config('app.info.name') ?></h1>
            <h2><?= config('app.info.description') ?></h2>
        </div>
        <div id="user_info">
            <p>
            <?php if (auth()->isIntact()) : ?>
                you are in, <?= auth()->user()?->first_name ?> &check;
            <?php else : ?>
                aloha, wanderer
            <?php endif; ?>
            </p>
        </div>
    </header>
    <main>
