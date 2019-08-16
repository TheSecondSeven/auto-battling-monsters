<div class="ultimates index">
	<h2><?php echo __('Ultimates'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<thead>
	<tr>
			<th><?php echo $this->Paginator->sort('rarity'); ?></th>
			<th><?php echo $this->Paginator->sort('type_id'); ?></th>
			<th><?php echo $this->Paginator->sort('secondary_type_id'); ?></th>
			<th><?php echo $this->Paginator->sort('name'); ?></th>
			<th><?php echo $this->Paginator->sort('description'); ?></th>
			<th><?php echo $this->Paginator->sort('passive'); ?></th>
			<th><?php echo $this->Paginator->sort('charges_needed'); ?></th>
			<th><?php echo $this->Paginator->sort('starting_charges'); ?></th>
			<th><?php echo $this->Paginator->sort('cast_time'); ?></th>
			<th><?php echo $this->Paginator->sort('down_time'); ?></th>
			<th><?php echo $this->Paginator->sort('created'); ?></th>
			<th><?php echo $this->Paginator->sort('modified'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($ultimates as $ultimate): ?>
	<tr>
		<td><?php echo h($ultimate['Ultimate']['rarity']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($ultimate['Type']['name'], array('controller' => 'types', 'action' => 'view', $ultimate['Type']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($ultimate['SecondaryType']['name'], array('controller' => 'types', 'action' => 'view', $ultimate['SecondaryType']['id'])); ?>
		</td>
		<td><?php echo h($ultimate['Ultimate']['name']); ?>&nbsp;</td>
		<td><?php echo h($ultimate['Ultimate']['description']); ?>&nbsp;</td>
		<td><?php echo h($ultimate['Ultimate']['passive']); ?>&nbsp;</td>
		<td><?php echo h($ultimate['Ultimate']['charges_needed']); ?>&nbsp;</td>
		<td><?php echo h($ultimate['Ultimate']['starting_charges']); ?>&nbsp;</td>
		<td><?php echo h($ultimate['Ultimate']['cast_time']); ?>&nbsp;</td>
		<td><?php echo h($ultimate['Ultimate']['down_time']); ?>&nbsp;</td>
		<td><?php echo h($ultimate['Ultimate']['created']); ?>&nbsp;</td>
		<td><?php echo h($ultimate['Ultimate']['modified']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $ultimate['Ultimate']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $ultimate['Ultimate']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $ultimate['Ultimate']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $ultimate['Ultimate']['id']))); ?>
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
		<li><?php echo $this->Html->link(__('New Ultimate'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Types'), array('controller' => 'types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Type'), array('controller' => 'types', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Skill Effects'), array('controller' => 'skill_effects', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Skill Effect'), array('controller' => 'skill_effects', 'action' => 'add')); ?> </li>
	</ul>
</div>
