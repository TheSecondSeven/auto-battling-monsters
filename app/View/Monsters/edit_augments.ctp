<div class="monsters form">
<?php echo $this->Form->create('Monster'); ?>
	<fieldset>
		<legend><?php echo __('Edit Monster Runes'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo '<div class="input select"><label>Current 1st Skill: '.(empty($monster['Skill1']['name']) ? 'None' : $monster['Skill1']['name']).'</label>';
		echo $this->Form->input('augment_skill_1_id', ['label' => '1st Skill Rune', 'options' => $skill_1_augments]);
		echo '</div>';
		echo '<div class="input select"><label>Current 2nd Skill: '.(empty($monster['Skill2']['name']) ? 'None' : $monster['Skill2']['name']).'</label>';
		echo $this->Form->input('augment_skill_2_id', ['label' => '2nd Skill Rune', 'options' => $skill_2_augments]);
		echo '</div>';
		echo '<div class="input select"><label>Current 3rd Skill: '.(empty($monster['Skill3']['name']) ? 'None' : $monster['Skill3']['name']).'</label>';
		echo $this->Form->input('augment_skill_3_id', ['label' => '3rd Skill Rune', 'options' => $skill_3_augments]);
		echo '</div>';
		echo '<div class="input select"><label>Current 4th Skill: '.(empty($monster['Skill4']['name']) ? 'None' : $monster['Skill4']['name']).'</label>';
		echo $this->Form->input('augment_skill_4_id', ['label' => '4th Skill Rune', 'options' => $skill_4_augments]);
		echo '</div>';
		echo '<div class="input select"><label>Current Ultimate: '.(empty($monster['Ultimate']['name']) ? 'None' : $monster['Ultimate']['name']).'</label>';
		echo $this->Form->input('augment_ultimate_id', ['label' => 'Ultimate Rune', 'options' => $ultimate_augments]);
		echo '</div>';
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>

<?php echo $this->element('side_nav'); ?>