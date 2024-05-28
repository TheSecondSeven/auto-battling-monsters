<?php $this->extend('../layout/dashboard'); ?>
<div class="runes form">
    <h3>Upgrade Rune</h3>
    <p>Each subsequent upgrade for a category increases that category's cost.</p>
	<?= $this->Form->create($rune) ?>
	<?= $this->Form->control('id'); ?>
	<?= $this->Form->control('upgrade', ['options' => $upgrade_options]); ?>
    <?= $this->Form->submit(__('Upgrade')); ?>
    <?= $this->Form->end() ?>
</div>