<div class="users form">
<?php echo $this->Form->create('User'); ?>
	<fieldset>
		<legend><?php echo __('Login'); ?></legend>
	<?php
		echo $this->Form->input('email');
		echo $this->Form->input('password');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Login')); ?>
<?php echo $this->Html->link(__('Don\'t have an account? Click here to Register.'), array('controller' => 'users', 'action' => 'register')); ?>
</div>
