<span class="form_header">&nbsp;&#8801; Find the document </span>
<?php include_once app()->root().'/views/partials/flash-message.php'; ?>

<?php if (flash()->getType() === 'error') : ?>
    <p>
        <a href="<?= config('app.url').'search' ?>">
            <img src="<?= config('app.url').'img/error.png' ?>">
        </a>
    </p>
<?php else: ?>
    <?php if (count($data['results']) > 0) : ?>
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
                <?php foreach ($data['results'] as $i => $document) : ?>
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
        <div class="form">
            <form action="<?= config('app.url').'search/find' ?>" method="POST">
                <div class="form_section">
                    <label for="title" class="field_header">Document title</label>
                    <input type="text"
                           class="<?= errors('title') ? 'invalid' : '' ?>"
                           name="title"
                           id="title"
                           value="<?= show(old('title')) ?>">
                    <div class="error_message">
                        <small class="required"><?= errors('title') ?></small>
                    </div>
                </div>

                <div class="form_section">
                    <label for="type" class="field_header">Document type</label>
                    <select name="type" id="type">
                        <option value="">Select type</option>
                        <?php foreach (\Digitaliseme\Enumerations\DocumentType::values() as $type) : ?>
                            <option value="<?= $type ?>"
                                <?= old('type') === $type ? 'selected' : '' ?>
                            >
                                <?= $type ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="error_message">
                        <small class="required"><?= errors('type') ?></small>
                    </div>
                </div>

                <div class="form_section">
                    <label for="issue_date" class="field_header">Issue date</label>
                    <input type="date"
                           class="<?= errors('issue_date') ? 'invalid' : '' ?>"
                           name="issue_date"
                           id="issue_date"
                           value="<?= show(old('issue_date')) ?>">
                    <div class="error_message">
                        <small class="required"><?= errors('issue_date') ?></small>
                    </div>
                </div>

                <div class="form_section">
                    <label class="field_header">Issued by</label>

                    <div class="form_subsection">
                        <label for="issuer_name" class="field_header">Name</label>
                        <input type="text"
                               class="<?= errors('issuer_name') ? 'invalid' : '' ?>"
                               name="issuer_name"
                               id="issuer_name"
                               value="<?= show(old('issuer_name')) ?>">
                        <div class="error_message">
                            <small class="required"><?= errors('issuer_name') ?></small>
                        </div>
                    </div>
                </div>

                <div class="form_section">
                    <label for="storage" class="field_header">Physical storage</label>
                    <input type="text"
                           placeholder="where is the physical document stored?"
                           class="<?= errors('storage') ? 'invalid' : '' ?>"
                           name="storage"
                           id="storage"
                           value="<?= show(old('storage')) ?>">
                    <div class="error_message">
                        <small class="required"><?= errors('storage') ?></small>
                    </div>
                </div>

                <div class="form_section">
                    <label for="keywords" class="field_header">Keywords</label>
                    <input type="text"
                           placeholder="separate, with, commas"
                           class="<?= errors('keywords') ? 'invalid' : '' ?>"
                           name="keywords"
                           id="keywords"
                           value="<?= show(old('keywords')) ?>">
                    <div class="error_message">
                        <small class="required"><?= errors('keywords') ?></small>
                    </div>
                </div>

                <input type="submit" value="Find the document" name="findme">

                <input type="hidden" id="token" name="token" value="<?= $data['token'] ?>">
            </form>
        </div>
    <?php endif; ?>
<?php endif; ?>
