<span class="form_header">&nbsp;&#10045; Files to process </span>
<span class="<?= $data['status']; ?>"><?= $data['message']; ?></span>
<?php if (isset($data['uploads']) && $data['status'] === 'okay') : ?>
    <div class="tables">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Uploaded</th>
                    <th>File</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?= Core\Helper::drawUploadsTable($data['uploads']); ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php if (!isset($data['uploads']) && $data['status'] === 'okay') : ?>
    <p>
        <a href="<?= HOME.'uploads/create'; ?>">
            <img src="<?= HOME.'img/empty.png'; ?>">
        </a>
    </p>
<?php endif; ?>
<?php if ($data['status'] === 'error') : ?>
    <p>
        <a href="<?= HOME.'uploads'; ?>">
            <img src="<?= HOME.'img/error.png'; ?>">
        </a>
    </p>
<?php endif; ?>