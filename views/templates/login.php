<span class="form_header">&nbsp;&#9784; Log in </span>
<span class="<?= flash()->getType() === 'error' ? 'error' : 'okay' ?>">
    <?= flash()->getMessage() ?>
</span>
<div class="form">
    <form action="<?= config('app.url').'login/init' ?>" method="POST">
        <label for="username" class="field_header">Username</label>
        <span class="required"><?= errors('username') ?></span>
        <input type="text"
               class="<?= errors('username') ? 'invalid' : '' ?>"
               name="username"
               id="username"
               value="<?= $data['fields']['username'] ?>"
        >

        <label for="password" class="field_header">Password</label>
        <span class="required"><?= errors('password') ?></span>
        <input type="password"
               class="<?= errors('username') ? 'invalid' : '' ?>"
               name="password"
               id="password"
               value="<?= $data['fields']['password'] ?>"
        >

        <input type="submit" value="Log in" name="logme">

        <input type="hidden" id="token" name="token" value="<?= $data['token'] ?>">
    </form>
</div>
<div class="info">
    * Have no account? Sign up <a href="<?= config('app.url').'signup' ?>">here</a>.
</div>
