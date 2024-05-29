<?php $this->extend('../layout/dashboard'); ?>
<div class="skills form">
    <h3>Update Skill</h3>
    <div class="mb-3">
		<?= $this->Html->link(__('View Skill'), ['action' => 'view', $skill->id], ['class'=>'btn btn-primary']); ?>
	</div>
	<?= $this->Form->create($skill) ?>
	<?= $this->Form->control('rarity', ['options' => ['Common'=>'Common','Uncommon'=>'Uncommon','Rare'=>'Rare','Epic'=>'Epic','Legendary'=>'Legendary','Admin Only'=>'Admin Only']]); ?>
	<?= $this->Form->control('name'); ?>
	<?= $this->Form->control('type_id'); ?>
	<?= $this->Form->control('description'); ?>
	<?= $this->Form->control('cast_time'); ?>
	<?= $this->Form->control('down_time'); ?>
    <?= $this->Form->submit(__('Update Skill')); ?>
    <?= $this->Form->end() ?>
</div>