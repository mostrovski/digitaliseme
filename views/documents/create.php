<?php $metaTitle = 'Work on new document'; ?>
<span class="form_header">&nbsp;&#9998; Work on new document </span>
<?php include_once app()->root().'/views/partials/flash-message.php'; ?>

<?php if (flash()->getType() === 'error') : ?>
    <p>
        <a href="<?= config('app.url').'uploads' ?>">
            <img src="<?= config('app.url').'img/error.png' ?>" alt="Error">
        </a>
    </p>
<?php else: ?>
    <div class="form">
        <form action="<?= config('app.url').'documents/store' ?>" method="POST">
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
                <input type="hidden" name="fileId" value="<?= $_SESSION['upfile'] ?>">
            </div>

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

                    <label for="issuer_email" class="field_header">Email</label>
                    <input type="text"
                           class="<?= errors('issuer_email') ? 'invalid' : '' ?>"
                           name="issuer_email"
                           id="issuer_email"
                           value="<?= show(old('issuer_email')) ?>">
                    <div class="error_message">
                        <small class="required"><?= errors('issuer_email') ?></small>
                    </div>

                    <label for="issuer_phone" class="field_header">Phone</label>
                    <input type="text"
                           class="<?= errors('issuer_phone') ? 'invalid' : '' ?>"
                           name="issuer_phone"
                           id="issuer_phone"
                           value="<?= show(old('issuer_phone')) ?>">
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

            <input type="submit" value="Save new document" name="saveme">

            <input type="hidden" id="token" name="token" value="<?= $token ?>">
        </form>
</div>
<?php endif; ?>
