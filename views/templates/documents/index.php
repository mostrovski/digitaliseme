<span class="form_header">&nbsp;&#10065; Documents </span>
<span class="<?= $data['status']; ?>"><?= $data['message']; ?></span>
<?php if (isset($data['documents']) && $data['status'] === 'okay') : ?>
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
                <?= Core\Helper::drawDocumentsTable($data['documents']); ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php if (!isset($data['documents']) && $data['status'] === 'okay') : ?>
    <p>
        <a href="<?= HOME.'uploads/create'; ?>">
            <img src="<?= HOME.'img/empty.png'; ?>">
        </a>
    </p>
<?php endif; ?>

<?php if ($data['status'] === 'error') : ?>
    <p>
        <a href="<?= HOME.'documents'; ?>">
            <img src="<?= HOME.'img/error.png'; ?>">
        </a>
    </p>
<?php endif; ?>