<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>
        <?= config('app.info.name').' | '.($metaTitle ?? '') ?>
    </title>
    <link rel="stylesheet" href="<?= config('app.url').'css/main.css' ?>">
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
