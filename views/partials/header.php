<header>
    <div id="logo">
        <img src="<?= url('img/logo.png') ?>" alt="Logo">
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
