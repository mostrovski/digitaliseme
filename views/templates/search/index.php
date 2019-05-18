<span class="form_header">&nbsp;&#8801; Find the document </span>
<span class="<?= $data['status']; ?>"><?= $data['message']; ?></span>
<?php if (isset($data['results'])) : ?>
<div class="tables">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Type</th>
                <th>Saved</th>
                <th>Document</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?= Core\Helper::drawDocumentsTable($data['results']); ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
<?php if (!isset($data['results'])) : ?>
<div class="form">
    <form action="<?= HOME.'search/find'; ?>" method="POST">
        <label for="doctitle" class="field_header">Document Title </label>
        <span class="required"><?= $data['errors']['docTitle']; ?></span>
        <input type="text" class="<?= $data['classes']['docTitle']; ?>" name="doctitle" id="doctitle" value="<?= $data['fields']['docTitle']; ?>">

        <label for="created" class="field_header">Date of Creation </label>
        <span class="required"><?= $data['errors']['createdDate']; ?></span>
        <input type="date" class="<?= $data['classes']['createdDate']; ?>" name="created" id="created" value="<?= $data['fields']['createdDate']; ?>">

        <label class="field_header">Document Creator</label><br>
        <label for="agname">name </label>
        <span class="required"><?= $data['errors']['agentName']; ?></span>
        <input type="text" class="<?= $data['classes']['agentName']; ?>" name="agname" id="agname" value="<?= $data['fields']['agentName']; ?>">

        <label for="doctype" class="field_header">Document Type</label>
        <select name="doctype" id="doctype">
            <option value="" <?= ($data['selectedType'] == '') ? 'selected' : ''; ?>>

            </option>
            <?php foreach ($data['docTypes'] as $type) : ?>
                <option value="<?= $type->type; ?>" <?= ($data['selectedType'] === $type->type) ? 'selected' : ''; ?>>
                    <?= $type->type; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="storage" class="field_header">Physical Storage </label>
        <span class="required"><?= $data['errors']['storagePlace']; ?></span>
        <input type="text" class="<?= $data['classes']['storagePlace']; ?>" name="storage" id="storage" value="<?= $data['fields']['storagePlace']; ?>">

        <label for="keywords" class="field_header">Keywords </label>
        <span class="required"><?= $data['errors']['keywords']; ?></span>
        <input type="text" class="<?= $data['classes']['keywords']; ?>" name="keywords" id="keywords" value="<?= $data['fields']['keywords']; ?>" placeholder="separate, with, commas">

        <input type="submit" value="Find the document" name="findme">

        <input type="hidden" id="token" name="token" value="<?= $data['token']; ?>">
    </form>
</div>
<?php endif; ?>
