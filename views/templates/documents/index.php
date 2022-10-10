<span class="form_header">&nbsp;&#10065; Documents </span>
<span class="<?= $data['status'] ?>"><?= $data['message'] ?></span>
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
                <?= Digitaliseme\Core\Helper::drawDocumentsTable($data['documents']) ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php if (!isset($data['documents']) && $data['status'] === 'okay') : ?>
    <p>
        <a href="<?= config('app.url').'uploads/create' ?>">
            <img src="<?= config('app.url').'img/empty.png' ?>">
        </a>
    </p>
<?php endif; ?>

<?php if ($data['status'] === 'error') : ?>
    <p>
        <a href="<?= config('app.url').'documents' ?>">
            <img src="<?= config('app.url').'img/error.png' ?>">
        </a>
    </p>
<?php endif; ?>
