<?php $metaTitle = 'Documents'; ?>
<span class="form_header">&nbsp;&#10065; Documents </span>
<?php include_once app()->root().'/views/partials/flash-message.php'; ?>

<?php if (flash()->getType() === 'error') : ?>
    <p>
        <a href="<?= config('app.url').'documents' ?>">
            <img src="<?= config('app.url').'img/error.png' ?>" alt="Error">
        </a>
    </p>
<?php else: ?>
    <?php if (count($documents) > 0) : ?>
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
                <?php foreach ($documents as $i => $document) : ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= $document->title ?></td>
                        <td><?= $document->type ?></td>
                        <td><?= $document->updated_at ?></td>
                        <td>
                            <a class="green_button"
                               href="<?= config('app.url').'documents/'.$document->id ?>"
                            >
                                details
                            </a>
                        </td>
                        <td>
                            <a class="gray_button"
                               href="<?= config('app.url').'documents/'.$document->id.'/download' ?>"
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
                <img src="<?= config('app.url').'img/empty.png' ?>" alt="Empty">
            </a>
        </p>
    <?php endif; ?>
<?php endif; ?>
