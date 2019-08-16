<div class="monsters form">
<?php echo $this->Form->create('Monster'); ?>
	<fieldset>
		<legend><?php echo __('Edit Monster Skills'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo '<div class="input select"><label>Current Rune: '.(empty($monster['AugmentSkill1']['name']) ? 'None' : $monster['AugmentSkill1']['name']).'</label>';
		echo $this->Form->input('skill_1_id', ['label' => '1st Skill', 'options' => $skill_1_options]);
		echo '</div>';
		echo '<div class="input select"><label>Current Rune: '.(empty($monster['AugmentSkill2']['name']) ? 'None' : $monster['AugmentSkill2']['name']).'</label>';
		echo $this->Form->input('skill_2_id', ['label' => '2nd Skill', 'options' => $skill_2_options]);
		echo '</div>';
		echo '<div class="input select"><label>Current Rune: '.(empty($monster['AugmentSkill3']['name']) ? 'None' : $monster['AugmentSkill3']['name']).'</label>';
		echo $this->Form->input('skill_3_id', ['label' => '3rd Skill', 'options' => $skill_3_options]);
		echo '</div>';
		echo '<div class="input select"><label>Current Rune: '.(empty($monster['AugmentSkill4']['name']) ? 'None' : $monster['AugmentSkill4']['name']).'</label>';
		echo $this->Form->input('skill_4_id', ['label' => '4th Skill', 'options' => $skill_4_options]);
		echo '</div>';
		echo '<div class="input select"><label>Current Rune: '.(empty($monster['AugmentUltimate']['name']) ? 'None' : $monster['AugmentUltimate']['name']).'</label>';
		echo $this->Form->input('ultimate_id', ['label' => 'Ultimate', 'options' => $ultimate_options]);
		echo '</div>';
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>

<?php echo $this->element('side_nav'); ?>