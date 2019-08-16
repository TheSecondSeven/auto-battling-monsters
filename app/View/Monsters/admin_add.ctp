<div class="monsters form">
<?php echo $this->Form->create('Monster'); ?>
	<fieldset>
		<legend><?php echo __('Admin Add Monster'); ?></legend>
	<?php
		echo $this->Form->input('user_id');
		echo $this->Form->input('name');
		echo $this->Form->input('type_id');
		echo $this->Form->input('strength');
		echo $this->Form->input('agility');
		echo $this->Form->input('dexterity');
		echo $this->Form->input('intelligence');
		echo $this->Form->input('luck');
		echo $this->Form->input('vitality');
		echo $this->Form->input('skill_1_id');
		echo $this->Form->input('skill_2_id');
		echo $this->Form->input('skill_3_id');
		echo $this->Form->input('skill_4_id');
		echo $this->Form->input('ultimate_id');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Monsters'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User'), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Types'), array('controller' => 'types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Type'), array('controller' => 'types', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Skills'), array('controller' => 'skills', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Skill1'), array('controller' => 'skills', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Ultimate'), array('controller' => 'ultimates', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Ultimate'), array('controller' => 'ultimates', 'action' => 'add')); ?> </li>
	</ul>
</div>
