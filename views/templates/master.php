<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>
        <?= config('app.info.name').' | '.($metaTitle ?? '') ?>
    </title>
    <link rel="stylesheet" href="<?= url('css/main.css') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= url('img/icons/apple-touch-icon.png') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= url('img/icons/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= url('img/icons/favicon-16x16.png') ?>">
    <link rel="manifest" href="<?= url('site.webmanifest') ?>">
</head>
<body>
    <?php include_once app()->root().'/views/partials/header.php'; ?>
    <?php if (auth()->isIntact()) : ?>
        <?php include_once app()->root().'/views/partials/navigation.php'; ?>
    <?php endif; ?>
    <main>
        ___\content/___
    </main>
    <?php include_once app()->root().'/views/partials/footer.php'; ?>
</body>
</html>
