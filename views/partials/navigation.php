<?php if (Digitaliseme\Core\Helper::isUserLoggedIn()) : ?>
<div class="nav">
  <ul>
    <li><a href="<?= HOME.'uploads/create' ?>">upload new file</a></li>
    <li><a href="<?= HOME.'uploads' ?>">files to process</a></li>
    <li><a href="<?= HOME.'documents' ?>">documents</a></li>
    <li><a href="<?= HOME.'search' ?>">find the document</a></li>
    <li><a href="<?= HOME.'logout' ?>">logout</a></li>
  </ul>
</div>
<?php endif; ?>
