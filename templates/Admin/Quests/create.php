<?php $this->extend('../layout/dashboard'); ?>
<div class="quests form">
    <h3>Create<?php if(!empty($parent_quest->id)) { echo ' Quest after '.$parent_quest->title; }else{ echo ' First Quest'; } ?></h3>
	<?= $this->Form->create($quest) ?>
	<?= $this->Form->control('title'); ?>
	<?= $this->Form->control('description'); ?>
	<?= $this->Form->control('required_rest'); ?>
	<?= $this->Form->control('persistent'); ?>
    <?= $this->Form->submit(__('Create Quest')); ?>
    <?= $this->Form->end() ?>
</div>