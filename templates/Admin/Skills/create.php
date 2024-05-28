<?php $this->extend('../layout/dashboard'); ?>
<div class="skills form">
    <h3>Create Skill</h3>
	<?= $this->Form->create($skill) ?>
	<?= $this->Form->control('rarity', ['options' => ['Common'=>'Common','Uncommon'=>'Uncommon','Rare'=>'Rare','Epic'=>'Epic','Legendary'=>'Legendary','Admin Only'=>'Admin Only']]); ?>
	<?= $this->Form->control('name'); ?>
	<?= $this->Form->control('type_id'); ?>
	<?= $this->Form->control('description'); ?>
	<?= $this->Form->control('cast_time'); ?>
	<?= $this->Form->control('down_time'); ?>
    <?= $this->Form->submit(__('Create Skill')); ?>
    <?= $this->Form->end() ?>
</div>