<div class="augments form">
<?php echo $this->Form->create('Augment'); ?>
	<fieldset>
		<legend><?php echo __('Admin Edit Augment'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('rarity', ['options' => ['Common'=>'Common','Uncommon'=>'Uncommon','Rare'=>'Rare','Epic'=>'Epic','Legendary'=>'Legendary','Admin Only'=>'Admin Only']]);
		echo $this->Form->input('name');
		echo $this->Form->input('skill_1');
		echo $this->Form->input('skill_2');
		echo $this->Form->input('skill_3');
		echo $this->Form->input('skill_4');
		echo $this->Form->input('ultimate');
		echo $this->Form->input('type');
		echo $this->Form->input('amount_1');
		echo $this->Form->input('amount_2');
		echo $this->Form->input('amount_3');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Augment.id')), array('confirm' => __('Are you sure you want to delete # %s?', $this->Form->value('Augment.id')))); ?></li>
		<li><?php echo $this->Html->link(__('List Augments'), array('action' => 'index')); ?></li>
	</ul>
</div>
