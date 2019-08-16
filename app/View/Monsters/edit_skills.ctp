<div class="monsters form">
<?php echo $this->Form->create('Monster'); ?>
	<fieldset>
		<legend><?php echo __('Edit Monster Skills'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('skill_1_id', ['label' => '1st Skill', 'options' => $skill_options]);
		echo $this->Form->input('skill_2_id', ['label' => '2nd Skill', 'options' => $skill_options]);
		echo $this->Form->input('skill_3_id', ['label' => '3rd Skill', 'options' => $skill_options]);
		echo $this->Form->input('skill_4_id', ['label' => '4th Skill', 'options' => $skill_options]);
		echo $this->Form->input('ultimate_id', ['label' => 'Ultimate', 'options' => $ultimate_options]);
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>

<?php echo $this->element('side_nav'); ?>