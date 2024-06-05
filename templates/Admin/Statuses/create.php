<?php $this->extend('../layout/dashboard'); ?>
<div class="statuses form">
    <h3>Create Status</h3>
	<?= $this->Form->create($status) ?>
	<?= $this->Form->control('type', ['options' => $types]); ?>
	<?= $this->Form->control('class'); ?>
	<?= $this->Form->control('name'); ?>
	<?= $this->Form->control('effect', ['options' => $effects]); ?>
	<?= $this->Form->control('description'); ?>
	<?= $this->Form->control('hex', ['type' => 'color']); ?>
	<?= $this->Form->control('text_color', ['options' => ['white' => 'White', 'Black' => 'Black']]); ?>
    <?= $this->Form->submit(__('Create Status')); ?>
    <?= $this->Form->end() ?>
</div>