<span class="form_header">&nbsp;&#10000; Edit document</span>
<span class="<?= $data['status'] ?>"><?= $data['message'] ?></span>
<?php if ($data['status'] === 'okay') : ?>
<div class="form">
    <form action="<?= config('app.url').'documents/update/'.$data['docId'] ?>" method="POST">
        <label for="doctitle" class="field_header">Document Title </label>
        <span class="required"><?= $data['errors']['docTitle'] ?></span>
        <input type="text" class="<?= $data['classes']['docTitle'] ?>" name="doctitle" id="doctitle" value="<?= $data['fields']['docTitle'] ?>">

        <label for="fname" class="field_header">File Name </label>
        <span class="required"><?= $data['errors']['fileName'] ?></span>
        <input type="text" class="<?= $data['classes']['fileName'] ?>" name="fname" id="fname" value="<?= $data['fields']['fileName'] ?>">

        <label for="created" class="field_header">Date of Creation </label>
        <span class="required"><?= $data['errors']['createdDate'] ?></span>
        <input type="date" class="<?= $data['classes']['createdDate'] ?>" name="created" id="created" value="<?= $data['fields']['createdDate'] ?>">

        <label class="field_header">Document Creator</label><br>

        <label for="agname">name </label>
        <span class="required"><?= $data['errors']['agentName'] ?></span>
        <input type="text" class="<?= $data['classes']['agentName'] ?>" name="agname" id="agname" value="<?= $data['fields']['agentName'] ?>">

        <label for="agemail">email </label>
        <span class="required"><?= $data['errors']['agentEmail'] ?></span>
        <input type="text" class="<?= $data['classes']['agentEmail'] ?>" name="agemail" id="agemail" value="<?= $data['fields']['agentEmail'] ?>">

        <label for="agphone">phone </label>
        <span class="required"><?= $data['errors']['agentPhone'] ?></span>
        <input type="text" class="<?= $data['classes']['agentPhone'] ?>" name="agphone" id="agphone" value="<?= $data['fields']['agentPhone'] ?>">

        <label for="doctype" class="field_header">Document Type</label>
        <select name="doctype" id="doctype">
            <?php foreach ($data['docTypes'] as $type) : ?>
                <option value="<?= $type->type ?>" <?= ($data['selectedType'] === $type->type) ? 'selected' : '' ?>>
                    <?= $type->type ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="storage" class="field_header">Physical Storage </label>
        <span class="required"><?= $data['errors']['storagePlace'] ?></span>
        <input type="text" class="<?= $data['classes']['storagePlace'] ?>" name="storage" id="storage" value="<?= $data['fields']['storagePlace'] ?>">

        <label for="keywords" class="field_header">Keywords </label>
        <span class="required"><?= $data['errors']['keywords'] ?></span>
        <input type="text" class="<?= $data['classes']['keywords'] ?>" name="keywords" id="keywords" value="<?= $data['fields']['keywords'] ?>">

        <input type="submit" value="Update document" name="updateme" onclick="return confirm('Update this document?')">

        <input type="hidden" id="token" name="token" value="<?= $data['token'] ?>">
    </form>
    <form action="<?= config('app.url').'documents/delete/'.$data['docId'] ?>" method="POST">
        <input type="submit" class="delete" value="Delete document" name="deleteme" onclick="return confirm('Delete this document?')">

        <input type="hidden" id="token" name="token" value="<?= $data['token'] ?>">
    </form>
</div>
<?php endif; ?>
<?php if ($data['status'] === 'error') : ?>
    <p>
        <a href="<?= config('app.url').'documents' ?>">
            <img src="<?= config('app.url').'img/error.png' ?>">
        </a>
    </p>
<?php endif; ?>
