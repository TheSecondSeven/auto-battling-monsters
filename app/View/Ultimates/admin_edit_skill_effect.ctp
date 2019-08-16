<div class="skillEffects form">
<?php echo $this->Form->create('SkillEffect'); ?>
	<fieldset>
		<legend><?php echo __('Admin Edit Effect'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('skill_effect_id', ['label' => 'Secondary Effect Of']);
		echo $this->Form->input('effect', ['options' => SkillEffect::effects()]);
		echo $this->Form->input('status', ['options' => $statuses]);
		echo $this->Form->input('amount_min');
		echo $this->Form->input('amount_max');
		echo $this->Form->input('duration');
		echo $this->Form->input('targets', ['options' => SkillEffect::targets()]);
		echo $this->Form->input('chance');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
	</ul>
</div>
