<span class="form_header">&nbsp;&#9784; Sign up </span>
<span class="<?= $data['status'] ?>"><?= $data['message']; ?></span>
<div class="form">
    <form action="<?= HOME.'signup/init'; ?>" method="POST">
        <label for="fname" class="field_header">First Name </label>
        <span class="required"><?= $data['errors']['firstname']; ?></span>
        <input type="text" class="<?= $data['classes']['firstname']; ?>" name="fname" id="fname" value="<?= $data['fields']['firstname']; ?>">

        <label for="lname" class="field_header">Last Name </label>
        <span class="required"><?= $data['errors']['lastname']; ?></span>
        <input type="text" class="<?= $data['classes']['lastname']; ?>" name="lname" id="lname" value="<?= $data['fields']['lastname']; ?>">

        <label for="email" class="field_header">Email </label>
        <span class="required"><?= $data['errors']['email']; ?></span>
        <input type="text" class="<?= $data['classes']['email']; ?>" name="email" id="email" value="<?= $data['fields']['email']; ?>">

        <label for="username" class="field_header">User Name </label>
        <span class="required"><?= $data['errors']['username']; ?></span>
        <input type="text" class="<?= $data['classes']['username']; ?>" name="username" id="username" value="<?= $data['fields']['username']; ?>">

        <label for="password" class="field_header">Password </label>
        <span class="required"><?= $data['errors']['password']; ?></span>
        <input type="password" class="<?= $data['classes']['password']; ?>" name="password" id="password" value="<?= $data['fields']['password']; ?>">

        <input type="submit" value="Sign up" name="signme">

        <input type="hidden" id="token" name="token" value="<?= $data['token']; ?>">
    </form>
</div>
<div class="info">
    * Already a user? Log in <a href="<?= HOME.'login'; ?>">here</a>.
</div>