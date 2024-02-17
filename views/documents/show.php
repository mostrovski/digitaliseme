<?php $metaTitle = 'Document details'; ?>
<span class="form_header">&nbsp;&#9776; Details</span>
<?php include_once app()->root().'/views/partials/flash-message.php'; ?>

<?php if (flash()->getType() === 'error') : ?>
    <p>
        <a href="<?= url('documents') ?>">
            <img src="<?= url('img/error.png') ?>" alt="error">
        </a>
    </p>
<?php else: ?>
    <div class="form">
        <form>
            <div class="form_section">
                <label for="filename" class="field_header">File Name</label>
                <input type="text" name="filename" id="filename" value="<?= $filename ?>" readonly>
            </div>

            <div class="form_section">
                <label for="title" class="field_header">Document Title</label>
                <input type="text" name="title" id="title" value="<?= $document->title ?>" readonly>
            </div>

            <div class="form_section">
                <label for="type" class="field_header">Document Type</label>
                <input type="text" name="type" id="type" value="<?= $document->type ?>" readonly>
            </div>

            <div class="form_section">
                <label for="issue_date" class="field_header">Issue date</label>
                <input type="date" name="issue_date" id="issue_date" value="<?= $document->issue_date ?>" readonly>
            </div>

            <div class="form_section">
                <label class="field_header">Issued by</label><br>

                <div class="form_subsection">
                    <label for="issuer_name">Name</label>
                    <input type="text" name="issuer_name" id="issuer_name" value="<?= $issuer?->name ?>" readonly>

                    <label for="issuer_email">Email</label>
                    <input type="text" name="issuer_email" id="issuer_email" value="<?= $issuer?->email ?>" readonly>

                    <label for="issuer_phone">Phone</label>
                    <input type="text" name="issuer_phone" id="issuer_phone" value="<?= $issuer?->phone ?>" readonly>
                </div>
            </div>

            <div class="form_section">
                <label for="storage" class="field_header">Physical Storage</label>
                <input type="text" name="storage" id="storage" value="<?= $storage ?>" readonly>
            </div>

            <div class="form_section">
                <label for="keywords" class="field_header">Keywords</label>
                <input type="text" name="keywords" id="keywords" value="<?= $keywords ?>" readonly>
            </div>
        </form>
        <?php if ($document->user_id === auth()->id()) : ?>
            <a href="<?= url('documents/'.$document->id.'/edit') ?>">
                <button>Edit</button>
            </a>
        <?php endif; ?>
    </div>
<?php endif; ?>
