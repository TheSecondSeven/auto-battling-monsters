<div class="users form">
<?php echo $this->Form->create('Rune'); ?>
	<fieldset>
		<legend><?php echo __('Upgrade Rune'); ?></legend>
		<h3>Each subsequent upgrade for a category increases that category's cost.</h3>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('upgrade', ['options' => $upgrade_options]);
	?>
	</fieldset>
<?php echo $this->Form->end(__('Upgrade')); ?>
</div>

<?php echo $this->element('side_nav'); ?>