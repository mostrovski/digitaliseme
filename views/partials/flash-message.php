<?php $__fm__ = flash()->getMessage(); ?>
<?php if (! empty ($__fm__)): ?>
    <span class="<?= flash()->getType() ?>">
        <?= '&#8921; '.$__fm__ ?>
    </span>
<?php endif; ?>
