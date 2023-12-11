<span class="form_header">&nbsp;&#10045; Files to process </span>
<?php include_once app()->root().'/views/partials/flash-message.php'; ?>

<?php if (flash()->getType() === 'error') : ?>
    <p>
        <a href="<?= config('app.url').'uploads' ?>">
            <img src="<?= config('app.url').'img/error.png' ?>">
        </a>
    </p>
<?php else: ?>
    <?php if (count($data['uploads']) > 0) : ?>
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
                <?php foreach ($data['uploads'] as $i => $upload) : ?>
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
                               href="<?= config('app.url').'documents/create/'.$upload->id ?>"
                            >
                                process
                            </a>
                        </td>
                        <td>
                            <a class="red_button"
                               href="<?= config('app.url').'uploads/delete/'.$upload->id ?>"
                               onclick="return confirm('Delete this file?')"
                            >
                                delete
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
