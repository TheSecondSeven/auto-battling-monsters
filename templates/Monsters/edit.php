<?php $this->extend('../layout/TwitterBootstrap/dashboard'); ?>
<div class="monsters form">
    <h3>Edit Monster</h3>
	<?= $this->Form->create($monster) ?>
	<?= $this->Form->control('id'); ?>
	<?= $this->Form->control('name'); ?>
    <?= $this->Form->submit('Save'); ?>
    <?= $this->Form->end() ?>
</div>