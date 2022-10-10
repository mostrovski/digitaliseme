<?php if (Digitaliseme\Core\Helper::isUserLoggedIn()) : ?>
<div class="nav">
  <ul>
    <li><a href="<?= config('app.url').'uploads/create' ?>">upload new file</a></li>
    <li><a href="<?= config('app.url').'uploads' ?>">files to process</a></li>
    <li><a href="<?= config('app.url').'documents' ?>">documents</a></li>
    <li><a href="<?= config('app.url').'search' ?>">find the document</a></li>
    <li><a href="<?= config('app.url').'logout' ?>">logout</a></li>
  </ul>
</div>
<?php endif; ?>
