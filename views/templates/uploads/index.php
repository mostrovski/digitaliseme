<span class="form_header">&nbsp;&#10045; Files to process </span>
<span class="<?= $data['status'] ?>"><?= $data['message'] ?></span>
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
                <?= Digitaliseme\Core\Helper::drawUploadsTable($data['uploads']) ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php if (!isset($data['uploads']) && $data['status'] === 'okay') : ?>
    <p>
        <a href="<?= config('app.url').'uploads/create' ?>">
            <img src="<?= config('app.url').'img/empty.png' ?>">
        </a>
    </p>
<?php endif; ?>
<?php if ($data['status'] === 'error') : ?>
    <p>
        <a href="<?= config('app.url').'uploads' ?>">
            <img src="<?= config('app.url').'img/error.png' ?>">
        </a>
    </p>
<?php endif; ?>
