<div class="effects form">
<?php echo $this->Form->create('Effect'); ?>
	<fieldset>
		<legend><?php echo __('Admin Add Effect'); ?></legend>
	<?php
		echo $this->Form->input('name');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Effects'), array('action' => 'index')); ?></li>
	</ul>
</div>
