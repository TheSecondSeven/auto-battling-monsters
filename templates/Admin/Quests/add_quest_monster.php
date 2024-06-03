<?php $this->extend('../layout/dashboard'); ?>
<div class="monsters form">
    <h3>Add Quest Monster </h3>
	<?= $this->Form->create($quest_monster) ?>
	<?= $this->Form->control('name'); ?>
	<?= $this->Form->control('skill_1_id', ['label' => '1st Skill', 'options' => $skills]); ?>
	<?= $this->Form->control('skill_2_id', ['label' => '2nd Skill', 'options' => $skills]); ?>
	<?= $this->Form->control('skill_3_id', ['label' => '3rd Skill', 'options' => $skills]); ?>
	<?= $this->Form->control('skill_4_id', ['label' => '4th Skill', 'options' => $skills]); ?>
	<?= $this->Form->control('ultimate_id', ['label' => 'Ultimate', 'options' => $ultimates]); ?>
    <?= $this->Form->submit('Add Monster'); ?>
    <?= $this->Form->end() ?>
</div>