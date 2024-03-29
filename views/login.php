<?php $metaTitle = 'Log in'; ?>
<span class="form_header">&nbsp;&#9784; Log in </span>
<?php include_once app()->root().'/views/partials/flash-message.php'; ?>
<div class="form">
    <form action="<?= url('login') ?>" method="POST">
        <div class="form_section">
            <label for="username" class="field_header">Username</label>
            <input type="text"
                   class="<?= errors('username') ? 'invalid' : '' ?>"
                   name="username"
                   id="username"
                   value="<?= show(old('username')) ?>"
            >
            <div class="error_message">
                <small class="required"><?= errors('username') ?></small>
            </div>
        </div>

        <div class="form_section">
            <label for="password" class="field_header">Password</label>
            <input type="password"
                   class="<?= errors('password') ? 'invalid' : '' ?>"
                   name="password"
                   id="password"
            >
            <div class="error_message">
                <small class="required"><?= errors('password') ?></small>
            </div>
        </div>

        <input type="submit" value="Log in" name="logme">

        <?= formToken() ?>
    </form>
</div>
<div class="info">
    * Have no account? Sign up <a href="<?= url('signup') ?>">here</a>.
</div>
