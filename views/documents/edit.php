<?php $metaTitle = 'Edit document'; ?>
<span class="form_header">&nbsp;&#10000; Edit document</span>
<?php include_once app()->root().'/views/partials/flash-message.php'; ?>

<?php if (flash()->getType() === 'error') : ?>
    <p>
        <a href="<?= url('documents') ?>">
            <img src="<?= url('img/error.png') ?>" alt="Error">
        </a>
    </p>
<?php else: ?>
    <div class="form">
        <form action="<?= url('documents/'.$document->id) ?>" method="POST">
            <?= formAltMethod('PATCH') ?>
            <div class="form_section">
                <label for="filename" class="field_header">Filename</label>
                <input type="text"
                       class="<?= errors('filename') ? 'invalid' : '' ?>"
                       name="filename"
                       id="filename"
                       value="<?= show(old('filename') ?? $filename) ?>">
                <div class="error_message">
                    <small class="required"><?= errors('filename') ?></small>
                </div>
            </div>

            <div class="form_section">
                <label for="title" class="field_header">Document title</label>
                <input type="text"
                       class="<?= errors('title') ? 'invalid' : '' ?>"
                       name="title"
                       id="title"
                       value="<?= show(old('title') ?? $document->title) ?>">
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
                            <?= (old('type') ?? $document->type) === $type ? 'selected' : '' ?>
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
                       value="<?= show(old('issue_date') ?? $document->issue_date) ?>">
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
                           value="<?= show(old('issuer_name') ?? $issuer?->name) ?>">
                    <div class="error_message">
                        <small class="required"><?= errors('issuer_name') ?></small>
                    </div>

                    <label for="issuer_email" class="field_header">Email</label>
                    <input type="text"
                           class="<?= errors('issuer_email') ? 'invalid' : '' ?>"
                           name="issuer_email"
                           id="issuer_email"
                           value="<?= show(old('issuer_email') ?? $issuer?->email) ?>">
                    <div class="error_message">
                        <small class="required"><?= errors('issuer_email') ?></small>
                    </div>

                    <label for="issuer_phone" class="field_header">Phone</label>
                    <input type="text"
                           class="<?= errors('issuer_phone') ? 'invalid' : '' ?>"
                           name="issuer_phone"
                           id="issuer_phone"
                           value="<?= show(old('issuer_phone') ?? $issuer?->phone) ?>">
                    <div class="error_message">
                        <small class="required"><?= errors('issuer_phone') ?></small>
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
                       value="<?= show(old('storage') ?? $storage) ?>">
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
                       value="<?= show(old('keywords') ?? $keywords) ?>">
                <div class="error_message">
                    <small class="required"><?= errors('keywords') ?></small>
                </div>
            </div>

            <input type="submit" value="Update document" name="updateme" onclick="return confirm('Update this document?')">

            <?= formToken() ?>
        </form>
        <form action="<?= url('documents/'.$document->id) ?>" method="POST">
            <?= formAltMethod('DELETE') ?>
            <input type="submit" class="delete" value="Delete document" name="deleteme" onclick="return confirm('Delete this document?')">
            <?= formToken() ?>
        </form>
    </div>
<?php endif; ?>
