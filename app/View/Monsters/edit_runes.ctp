<div class="monsters form">
<?php echo $this->Form->create('Monster'); ?>
	<fieldset>
		<legend><?php echo __('Edit Monster Runes'); ?></legend>
	<?php 
		
		if($monster['Monster']['rune_level'] < 3) {
			echo $this->Html->link(__('Purchase Another Rune Slot ('.($monster['Monster']['rune_level'] == 1 ? 250 : 1000).' Gold)'), array('controller' => 'monsters', 'action' => 'increase_rune_level', $monster['Monster']['id']));
		}
		echo $this->Form->input('id');
		echo $this->Form->input('rune_1_id', ['label' => '1st Rune', 'options' => $runes]);
		if($monster['Monster']['rune_level'] > 1) {
			echo $this->Form->input('rune_2_id', ['label' => '2nd Rune', 'options' => $runes]);
		}else{
			echo $this->Form->input('rune_2_id', ['type' => 'hidden']);
		}
		if($monster['Monster']['rune_level'] > 2) {
			echo $this->Form->input('rune_3_id', ['label' => '3rd Rune', 'options' => $runes]);
		}else{
			echo $this->Form->input('rune_3_id', ['type' => 'hidden']);
		}
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>

<?php echo $this->element('side_nav'); ?>