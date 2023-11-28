<span class="form_header">&nbsp;&#128441; Details</span>
<span class="<?= $data['status'] ?>"><?= $data['message'] ?></span>
<?php if ($data['status'] === 'okay') : ?>
<div class="form">
    <form>
        <label for="doctitle" class="field_header">Document Title </label>
        <input type="text" name="doctitle" id="doctitle" value="<?= $data['fields']['docTitle'] ?>" readonly>

        <label for="first_name" class="field_header">File Name </label>
        <input type="text" name="first_name" id="first_name" value="<?= $data['fields']['fileName'] ?>" readonly>

        <label for="created" class="field_header">Date of Creation </label>
        <input type="date" name="created" id="created" value="<?= $data['fields']['createdDate'] ?>" readonly>

        <label class="field_header">Document Creator</label><br>

        <label for="agname">name </label>
        <input type="text" name="agname" id="agname" value="<?= $data['fields']['agentName'] ?>" readonly>

        <label for="agemail">email </label>
        <input type="text" name="agemail" id="agemail" value="<?= $data['fields']['agentEmail'] ?>" readonly>

        <label for="agphone">phone </label>
        <input type="text" name="agphone" id="agphone" value="<?= $data['fields']['agentPhone'] ?>" readonly>

        <label for="doctype" class="field_header">Document Type</label>
        <input type="text" name="doctype" id="doctype" value="<?= $data['selectedType'] ?>" readonly>

        <label for="storage" class="field_header">Physical Storage </label>
        <input type="text" name="storage" id="storage" value="<?= $data['fields']['storagePlace'] ?>" readonly>

        <label for="keywords" class="field_header">Keywords </label>
        <input type="text" name="keywords" id="keywords" value="<?= $data['fields']['keywords'] ?>" readonly>
    </form>
    <?php if ($data['userId'] === $_SESSION['loggedinID']) : ?>
        <a href="<?= config('app.url').'documents/edit/'.$data['docId'] ?>">
            <button>Edit</button>
        </a>
    <?php endif; ?>
</div>
<?php endif; ?>
<?php if ($data['status'] === 'error') : ?>
    <p>
        <a href="<?= config('app.url').'documents' ?>">
            <img src="<?= config('app.url').'img/error.png' ?>">
        </a>
    </p>
<?php endif; ?>
