<div class="users form">
<?php echo $this->Form->create('Rune'); ?>
	<fieldset>
		<legend><?php echo __('Create Rune'); ?></legend>
	<?php
		echo $this->Form->input('type_id');
		echo $this->Form->input('first_upgrade', ['options' => $upgrade_options]);
	?>
	</fieldset>
<?php echo $this->Form->end(__('Create')); ?>
</div>

<?php echo $this->element('side_nav'); ?>