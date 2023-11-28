<span class="form_header">&nbsp;&#9784; Sign up </span>
<span class="<?= flash()->getType() === 'error' ? 'error' : 'okay' ?>">
    <?= flash()->getMessage() ?>
</span>
<div class="form">
    <form action="<?= config('app.url').'signup/init' ?>" method="POST">
        <label for="first_name" class="field_header">First Name </label>
        <span class="required"><?= errors('first_name') ?></span>
        <input type="text"
               class="<?= errors('first_name') ? 'invalid' : '' ?>"
               name="first_name"
               id="first_name"
               value="<?= $data['fields']['first_name'] ?>"
        >

        <label for="last_name" class="field_header">Last Name </label>
        <span class="required"><?= errors('last_name') ?></span>
        <input type="text"
               class="<?= errors('last_name') ? 'invalid' : '' ?>"
               name="last_name"
               id="last_name"
               value="<?= $data['fields']['last_name'] ?>"
        >

        <label for="email" class="field_header">Email </label>
        <span class="required"><?= errors('email') ?></span>
        <input type="text"
               class="<?= errors('email') ? 'invalid' : '' ?>"
               name="email"
               id="email"
               value="<?= $data['fields']['email'] ?>"
        >

        <label for="username" class="field_header">User Name </label>
        <span class="required"><?= errors('username') ?></span>
        <input type="text"
               class="<?= errors('username') ? 'invalid' : '' ?>"
               name="username"
               id="username"
               value="<?= $data['fields']['username'] ?>"
        >

        <label for="password" class="field_header">Password </label>
        <span class="required"><?= errors('password') ?></span>
        <input type="password"
               class="<?= errors('password') ? 'invalid' : '' ?>"
               name="password"
               id="password"
               value="<?= $data['fields']['password'] ?>"
        >

        <input type="submit" value="Sign up" name="signme">

        <input type="hidden" id="token" name="token" value="<?= $data['token'] ?>">
    </form>
</div>
<div class="info">
    * Already a user? Log in <a href="<?= config('app.url').'login' ?>">here</a>.
</div>
