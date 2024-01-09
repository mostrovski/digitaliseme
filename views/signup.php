<?php $metaTitle = 'Sign up'; ?>
<span class="form_header">&nbsp;&#9784; Sign up </span>
<?php include_once app()->root().'/views/partials/flash-message.php'; ?>
<div class="form">
    <form action="<?= config('app.url').'signup/init' ?>" method="POST">
        <div class="form_section">
            <label for="first_name" class="field_header">First Name </label>
            <input type="text"
                   class="<?= errors('first_name') ? 'invalid' : '' ?>"
                   name="first_name"
                   id="first_name"
                   value="<?= show(old('first_name')) ?>"
            >
            <div class="error_message">
                <small class="required"><?= errors('first_name') ?></small>
            </div>
        </div>

        <div class="form_section">
            <label for="last_name" class="field_header">Last Name </label>
            <input type="text"
                   class="<?= errors('last_name') ? 'invalid' : '' ?>"
                   name="last_name"
                   id="last_name"
                   value="<?= show(old('last_name')) ?>"
            >
            <div class="error_message">
                <small class="required"><?= errors('last_name') ?></small>
            </div>
        </div>

        <div class="form_section">
            <label for="email" class="field_header">Email </label>
            <input type="text"
                   class="<?= errors('email') ? 'invalid' : '' ?>"
                   name="email"
                   id="email"
                   value="<?= show(old('email')) ?>"
            >
            <div class="error_message">
                <small class="required"><?= errors('email') ?></small>
            </div>
        </div>

        <div class="form_section">
            <label for="username" class="field_header">Username </label>
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
            <label for="password" class="field_header">Password </label>
            <input type="password"
                   class="<?= errors('password') ? 'invalid' : '' ?>"
                   name="password"
                   id="password"
                   value=""
            >
            <div class="error_message">
                <small class="required"><?= errors('password') ?></small>
            </div>
        </div>

        <input type="submit" value="Sign up" name="signme">

        <?php include_once app()->root().'/views/partials/token.php'; ?>
    </form>
</div>
<div class="info">
    * Already a user? Log in <a href="<?= config('app.url').'login' ?>">here</a>.
</div>
