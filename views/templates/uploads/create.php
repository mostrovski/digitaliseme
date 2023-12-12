<span class="form_header">&nbsp;&#10064; Upload new file </span>
<?php include_once app()->root().'/views/partials/flash-message.php'; ?>
<div class="form">
    <form action="<?= config('app.url').'uploads/store' ?>" method="POST" enctype="multipart/form-data">
        <label for="docfile" class="field_header">
            Choose the file to upload
        </label>
        <input type="file" name="docfile" id="docfile" accept=".pdf, .jpg, .jpeg, .png">

        <p>
            <span class="info">&rarr; supported types: pdf, jpg, jpeg, png</span><br>
            <span class="info">&rarr; max size: 1 MB</span>
        </p>

        <input type="submit" value="Upload" name="upload">

        <input type="hidden" id="token" name="token" value="<?= $data['token'] ?>">
    </form>
</div>
