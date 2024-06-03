<?php $this->extend('../layout/dashboard'); ?>
<div class="rewards form">
    <h3>Add Quest Reward</h3>
	<?= $this->Form->create($quest_reward) ?>
	<?= $this->Form->control('reward_type', ['options' => $reward_types]); ?>
	<?= $this->Form->control('skill_id'); ?>
	<?= $this->Form->control('ultimate_id'); ?>
	<?= $this->Form->control('type_id', ['options' => $types]); ?>
	<?= $this->Form->control('secondary_type_id', ['options' => $types]); ?>
	<?= $this->Form->control('usable'); ?>
	<?= $this->Form->control('amount'); ?>
	<?= $this->Form->control('rarity', ['options' => ['Common'=>'Common','Uncommon'=>'Uncommon','Rare'=>'Rare','Epic'=>'Epic','Legendary'=>'Legendary']]); ?>
    <?= $this->Form->submit(__('Add')); ?>
    <?= $this->Form->end() ?>
</div>