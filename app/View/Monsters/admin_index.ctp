<div class="monsters index">
	<h2><?php echo __('Monsters'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<thead>
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('elo_rating'); ?></th>
			<th><?php echo $this->Paginator->sort('user_id'); ?></th>
			<th><?php echo $this->Paginator->sort('name'); ?></th>
			<th><?php echo $this->Paginator->sort('type_id'); ?></th>
			<th><?php echo $this->Paginator->sort('skill_1_id'); ?></th>
			<th><?php echo $this->Paginator->sort('skill_2_id'); ?></th>
			<th><?php echo $this->Paginator->sort('skill_3_id'); ?></th>
			<th><?php echo $this->Paginator->sort('skill_4_id'); ?></th>
			<th><?php echo $this->Paginator->sort('ultimate_id'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($monsters as $monster): ?>
	<tr>
		<td><?php echo h($monster['Monster']['id']); ?>&nbsp;</td>
		<td><?php echo h($monster['Monster']['elo_rating']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($monster['User']['username'], array('controller' => 'users', 'action' => 'view', $monster['User']['id'])); ?>
		</td>
		<td><?php echo h($monster['Monster']['name']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($monster['Type']['name'], array('controller' => 'types', 'action' => 'view', $monster['Type']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($monster['Skill1']['name'], array('controller' => 'skills', 'action' => 'view', $monster['Skill1']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($monster['Skill2']['name'], array('controller' => 'skills', 'action' => 'view', $monster['Skill2']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($monster['Skill3']['name'], array('controller' => 'skills', 'action' => 'view', $monster['Skill3']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($monster['Skill4']['name'], array('controller' => 'skills', 'action' => 'view', $monster['Skill4']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($monster['Ultimate']['name'], array('controller' => 'ultimates', 'action' => 'view', $monster['Ultimate']['id'])); ?>
		</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $monster['Monster']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $monster['Monster']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $monster['Monster']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $monster['Monster']['id']))); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</tbody>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
		'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Monster'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User'), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Types'), array('controller' => 'types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Type'), array('controller' => 'types', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Skills'), array('controller' => 'skills', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Skill'), array('controller' => 'skills', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Ultimate'), array('controller' => 'ultimates', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Ultimate'), array('controller' => 'ultimates', 'action' => 'add')); ?> </li>
	</ul>
</div>
