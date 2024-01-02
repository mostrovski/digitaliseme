<span class="form_header">&nbsp;&#10065; Documents </span>
<?php include_once app()->root().'/views/partials/flash-message.php'; ?>

<?php if (flash()->getType() === 'error') : ?>
    <p>
        <a href="<?= config('app.url').'documents' ?>">
            <img src="<?= config('app.url').'img/error.png' ?>">
        </a>
    </p>
<?php else: ?>
    <?php if (count($data['documents']) > 0) : ?>
        <div class="tables">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Updated at</th>
                        <th>Document</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($data['documents'] as $i => $document) : ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= $document->title ?></td>
                        <td><?= $document->type ?></td>
                        <td><?= $document->updated_at ?></td>
                        <td>
                            <a class="green_button"
                               href="<?= config('app.url').'documents/show/'.$document->id ?>"
                            >
                                details
                            </a>
                        </td>
                        <td>
                            <a class="gray_button"
                               href="<?= config('app.url').'documents/download/'.$document->id ?>"
                            >
                                download
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>
            <a href="<?= config('app.url').'uploads/create' ?>">
                <img src="<?= config('app.url').'img/empty.png' ?>">
            </a>
        </p>
    <?php endif; ?>
<?php endif; ?>
