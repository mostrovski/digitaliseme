<?php $metaTitle = 'Files to process'; ?>
<span class="form_header">&nbsp;&#10045; Files to process </span>
<?php include_once app()->root().'/views/partials/flash-message.php'; ?>

<?php if (flash()->getType() === 'error') : ?>
    <p>
        <a href="<?= url('uploads') ?>">
            <img src="<?= url('img/error.png') ?>" alt="Error">
        </a>
    </p>
<?php else: ?>
    <?php if (count($uploads) > 0) : ?>
        <div class="tables">
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Uploaded at</th>
                    <th>File</th>
                    <th></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($uploads as $i => $upload) : ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= $upload->filename ?></td>
                        <td><?= $upload->created_at ?></td>
                        <td>
                            <a class="gray_button"
                               target="_blank"
                               href="<?= $upload->publicPath() ?>"
                            >
                                preview
                            </a>
                        </td>
                        <td>
                            <a class="green_button"
                               href="<?= url('documents/create?fileId='.$upload->id) ?>"
                            >
                                process
                            </a>
                        </td>
                        <td>
                            <form action="<?= url('uploads/'.$upload->id) ?>" method="POST">
                                <?= formAltMethod('DELETE') ?>
                                <button class="red_button"
                                        onclick="return confirm('Delete this file?')"
                                >
                                    delete
                                </button>
                                <?= formToken() ?>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>
            <a href="<?= url('uploads/create') ?>">
                <img src="<?= url('img/empty.png') ?>" alt="Empty">
            </a>
        </p>
    <?php endif; ?>
<?php endif; ?>
