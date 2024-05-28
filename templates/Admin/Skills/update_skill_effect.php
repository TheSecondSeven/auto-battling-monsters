<?php $this->extend('../layout/dashboard'); ?>
<div class="skill_effects form">
    <h3>Update Skill Effect</h3>
	<?= $this->Form->create($skill_effect) ?>
	<?= $this->Form->control('id'); ?>
	<?= $this->Form->control('effect', ['options' => $effects]); ?>
	<?= $this->Form->control('status', ['options' => $statuses]); ?>
	<?= $this->Form->control('amount_min'); ?>
	<?= $this->Form->control('amount_max'); ?>
	<?= $this->Form->control('duration'); ?>
	<?= $this->Form->control('targets', ['options' => $targets]); ?>
	<?= $this->Form->control('chance'); ?>
    <?= $this->Form->submit(__('Update')); ?>
    <?= $this->Form->end() ?>
</div>