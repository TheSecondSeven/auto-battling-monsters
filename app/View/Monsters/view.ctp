<div class="monsters view">
<h2><?php echo __('Monster'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($monster['Monster']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('User'); ?></dt>
		<dd>
			<?php echo $this->Html->link($monster['User']['username'], array('controller' => 'users', 'action' => 'view', $monster['User']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($monster['Monster']['name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Type'); ?></dt>
		<dd>
			<?php echo $this->Html->link($monster['Type']['name'], array('controller' => 'types', 'action' => 'view', $monster['Type']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Strength'); ?></dt>
		<dd>
			<?php echo h($monster['Monster']['strength']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Agility'); ?></dt>
		<dd>
			<?php echo h($monster['Monster']['agility']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Dexterity'); ?></dt>
		<dd>
			<?php echo h($monster['Monster']['dexterity']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Intelligence'); ?></dt>
		<dd>
			<?php echo h($monster['Monster']['intelligence']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Luck'); ?></dt>
		<dd>
			<?php echo h($monster['Monster']['luck']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Vitality'); ?></dt>
		<dd>
			<?php echo h($monster['Monster']['vitality']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Skill1'); ?></dt>
		<dd>
			<?php echo $this->Html->link($monster['Skill1']['name'], array('controller' => 'skills', 'action' => 'view', $monster['Skill1']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Skill2'); ?></dt>
		<dd>
			<?php echo $this->Html->link($monster['Skill2']['name'], array('controller' => 'skills', 'action' => 'view', $monster['Skill2']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Skill3'); ?></dt>
		<dd>
			<?php echo $this->Html->link($monster['Skill3']['name'], array('controller' => 'skills', 'action' => 'view', $monster['Skill3']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Skill4'); ?></dt>
		<dd>
			<?php echo $this->Html->link($monster['Skill4']['name'], array('controller' => 'skills', 'action' => 'view', $monster['Skill4']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($monster['Monster']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($monster['Monster']['modified']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Monster'), array('action' => 'edit', $monster['Monster']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Monster'), array('action' => 'delete', $monster['Monster']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $monster['Monster']['id']))); ?> </li>
		<li><?php echo $this->Html->link(__('List Monsters'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Monster'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User'), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Types'), array('controller' => 'types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Type'), array('controller' => 'types', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Skills'), array('controller' => 'skills', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Skill1'), array('controller' => 'skills', 'action' => 'add')); ?> </li>
	</ul>
</div>
