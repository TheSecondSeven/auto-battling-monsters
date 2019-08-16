<div class="ultimates form">
<?php echo $this->Form->create('Ultimate'); ?>
	<fieldset>
		<legend><?php echo __('Admin Add Ultimate'); ?></legend>
	<?php
		echo $this->Form->input('rarity', ['options' => ['Common'=>'Common','Uncommon'=>'Uncommon','Rare'=>'Rare','Epic'=>'Epic','Legendary'=>'Legendary','Admin Only'=>'Admin Only']]);
		echo $this->Form->input('type_id');
		echo $this->Form->input('secondary_type_id');
		echo $this->Form->input('name');
		echo $this->Form->input('description');
		echo $this->Form->input('passive');
		echo $this->Form->input('charges_needed');
		echo $this->Form->input('starting_charges');
		echo $this->Form->input('cast_time');
		echo $this->Form->input('down_time');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Ultimates'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Types'), array('controller' => 'types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Type'), array('controller' => 'types', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Skill Effects'), array('controller' => 'skill_effects', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Skill Effect'), array('controller' => 'skill_effects', 'action' => 'add')); ?> </li>
	</ul>
</div>
