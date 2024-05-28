<?php $this->extend('../layout/TwitterBootstrap/dashboard'); ?>
<div class="monsters form">
    <h3>Edit Monster Skills</h3>
	<?= $this->Form->create($monster) ?>
	<?= $this->Form->control('id'); ?>
	<?= $this->Form->control('skill_1_id', ['label' => '1st Skill', 'options' => $skill_options]); ?>
	<?= $this->Form->control('skill_2_id', ['label' => '2nd Skill', 'options' => $skill_options]); ?>
	<?= $this->Form->control('skill_3_id', ['label' => '3rd Skill', 'options' => $skill_options]); ?>
	<?= $this->Form->control('skill_4_id', ['label' => '4th Skill', 'options' => $skill_options]); ?>
	<?= $this->Form->control('ultimate_id', ['label' => 'Ultimate', 'options' => $ultimate_options]); ?>
    <?= $this->Form->submit('Save Skills'); ?>
    <?= $this->Form->end() ?>
</div>