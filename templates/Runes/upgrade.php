<?php $this->extend('../layout/TwitterBootstrap/dashboard'); ?>
<div class="runes form">
    <legend><?php echo __('Upgrade Rune'); ?></legend>
    <h3>Each subsequent upgrade for a category increases that category's cost.</h3>
	<?= $this->Form->create($rune) ?>
	<?= $this->Form->control('id'); ?>
	<?= $this->Form->control('upgrade', ['options' => $upgrade_options]); ?>
    <?= $this->Form->submit(__('Upgrade')); ?>
    <?= $this->Form->end() ?>
</div>