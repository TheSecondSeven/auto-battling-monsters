<?php $this->extend('../layout/dashboard'); ?>
<div class="runes form">
    <h3>Create Rune</h3>
	<?= $this->Form->create($rune) ?>
	<?= $this->Form->control('type_id'); ?>
	<?= $this->Form->control('first_upgrade', ['options' => $upgrade_options]); ?>
    <?= $this->Form->submit(__('Create (5 Rune Shards)')); ?>
    <?= $this->Form->end() ?>
</div>