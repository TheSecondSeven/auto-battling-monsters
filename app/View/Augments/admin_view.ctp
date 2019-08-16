<div class="augments view">
<h2><?php echo __('Augment'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($augment['Augment']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Rarity'); ?></dt>
		<dd>
			<?php echo h($augment['Augment']['rarity']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($augment['Augment']['name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Skill 1'); ?></dt>
		<dd>
			<?php echo h($augment['Augment']['skill_1']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Skill 2'); ?></dt>
		<dd>
			<?php echo h($augment['Augment']['skill_2']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Skill 3'); ?></dt>
		<dd>
			<?php echo h($augment['Augment']['skill_3']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Skill 4'); ?></dt>
		<dd>
			<?php echo h($augment['Augment']['skill_4']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Ultimate'); ?></dt>
		<dd>
			<?php echo h($augment['Augment']['ultimate']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Type'); ?></dt>
		<dd>
			<?php echo h($augment['Augment']['type']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Amount 1'); ?></dt>
		<dd>
			<?php echo h($augment['Augment']['amount_1']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Amount 2'); ?></dt>
		<dd>
			<?php echo h($augment['Augment']['amount_2']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Amount 3'); ?></dt>
		<dd>
			<?php echo h($augment['Augment']['amount_3']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($augment['Augment']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($augment['Augment']['modified']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Augment'), array('action' => 'edit', $augment['Augment']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Augment'), array('action' => 'delete', $augment['Augment']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $augment['Augment']['id']))); ?> </li>
		<li><?php echo $this->Html->link(__('List Augments'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Augment'), array('action' => 'add')); ?> </li>
	</ul>
</div>
