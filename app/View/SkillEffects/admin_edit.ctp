<div class="skillEffects form">
<?php echo $this->Form->create('SkillEffect'); ?>
	<fieldset>
		<legend><?php echo __('Admin Edit Skill Effect'); ?></legend>
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

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('SkillEffect.id')), array('confirm' => __('Are you sure you want to delete # %s?', $this->Form->value('SkillEffect.id')))); ?></li>
		<li><?php echo $this->Html->link(__('List Skill Effects'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Skills'), array('controller' => 'skills', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Skill'), array('controller' => 'skills', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Skill Effects'), array('controller' => 'skill_effects', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Skill Effect'), array('controller' => 'skill_effects', 'action' => 'add')); ?> </li>
	</ul>
</div>
