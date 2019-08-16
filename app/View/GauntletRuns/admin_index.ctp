<div class="monsters index">
	<h2><?php echo __('Gauntlet Runs'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<thead>
	<tr>
			<th><?php echo $this->Paginator->sort('created'); ?></th>
			<th><?php echo $this->Paginator->sort('user_id'); ?></th>
			<th><?php echo $this->Paginator->sort('monster_id'); ?></th>
			<th><?php echo $this->Paginator->sort('wins'); ?></th>
			<th><?php echo $this->Paginator->sort('losses'); ?></th>
			<th><?php echo $this->Paginator->sort('ties'); ?></th>
			<th><?php echo $this->Paginator->sort('longest_streak'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php 
		foreach ($gauntlet_runs as $gauntlet_run): ?>
	<tr>
		<td><?php echo date('F jS, Y g:ia', strtotime($gauntlet_run['GauntletRun']['created'])); ?></td>
		<td><?php echo $this->Html->link($gauntlet_run['User']['username'], array('controller' => 'users', 'action' => 'view', $gauntlet_run['User']['id'])); ?></td>
		<td><?php echo $this->Html->link($gauntlet_run['Monster']['name'], array('controller' => 'monsters', 'action' => 'view', $gauntlet_run['Monster']['id'])); ?></td>
		<td><?php echo $gauntlet_run['GauntletRun']['wins']; ?>&nbsp;</td>
		<td><?php echo $gauntlet_run['GauntletRun']['losses']; ?>&nbsp;</td>
		<td><?php echo $gauntlet_run['GauntletRun']['ties']; ?>&nbsp;</td>
		<td><?php echo $gauntlet_run['GauntletRun']['longest_streak']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View Battles'), array('controller' => 'gauntlet_runs', 'action' => 'view_battles', $gauntlet_run['GauntletRun']['id'], 'admin' => false)); ?>
			<?php echo $this->Html->link(__('View Results'), array('controller' => 'gauntlet_runs', 'action' => 'view_results', $gauntlet_run['GauntletRun']['id'], 'admin' => false)); ?>
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
