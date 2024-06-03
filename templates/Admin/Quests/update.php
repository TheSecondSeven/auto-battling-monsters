<?php $this->extend('../layout/dashboard'); ?>
<div class="quests form">
    <h3>Update Quest</h3>
	<?= $this->Form->create($quest) ?>
	<?= $this->Form->control('title'); ?>
	<?= $this->Form->control('description'); ?>
	<?= $this->Form->control('required_rest'); ?>
    <?= $this->Form->submit(__('Update Quest')); ?>
    <?= $this->Form->end() ?>
</div>