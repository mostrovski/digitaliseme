<span class="form_header">&nbsp;&#9784; Log in </span>
<span class="<?= flash()->getType() ?>">
    <?= flash()->getMessage() ?>
</span>
<div class="form">
    <form action="<?= config('app.url').'login/init' ?>" method="POST">
        <div class="form_section">
            <label for="username" class="field_header">Username</label>
            <input type="text"
                   class="<?= errors('username') ? 'invalid' : '' ?>"
                   name="username"
                   id="username"
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

        <input type="hidden" id="token" name="token" value="<?= $data['token'] ?>">
    </form>
</div>
<div class="info">
    * Have no account? Sign up <a href="<?= config('app.url').'signup' ?>">here</a>.
</div>
