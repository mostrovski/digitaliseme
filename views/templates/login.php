<span class="form_header">&nbsp;&#9784; Log in </span>
<span class="<?= $data['classes']['message'] ?>"><?= $data['message'] ?></span>
<div class="form">
    <form action="<?= HOME.'login/init' ?>" method="POST">
        <label for="username" class="field_header">User Name</label>
        <input type="text" class="<?= $data['classes']['fields'] ?>" name="username" id="username" value="<?= $data['fields']['username'] ?>">

        <label for="password" class="field_header">Password</label>
        <input type="password" class="<?= $data['classes']['fields'] ?>" name="password" id="password" value="<?= $data['fields']['password'] ?>">

        <input type="submit" value="Log in" name="logme">

        <input type="hidden" id="token" name="token" value="<?= $data['token'] ?>">
    </form>
</div>
<div class="info">
    * Have no account? Sign up <a href="<?= HOME.'signup' ?>">here</a>.
</div>
