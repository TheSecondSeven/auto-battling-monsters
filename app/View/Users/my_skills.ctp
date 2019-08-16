<div class="skills index">
	<h2><?php echo __('My Skills'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<thead>
	<tr>
		<th><?php echo $this->Paginator->sort('rarity'); ?></th>
		<th><?php echo $this->Paginator->sort('name'); ?></th>
		<th><?php echo $this->Paginator->sort('type_id'); ?></th>
		<th><?php echo $this->Paginator->sort('description'); ?></th>
		<th><?php echo $this->Paginator->sort('cast_time'); ?></th>
		<th><?php echo $this->Paginator->sort('down_time'); ?></th>
		<th class="actions"></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($skills as $skill): ?>
	<tr>
		<td><?php echo h($skill['Skill']['rarity']); ?>&nbsp;</td>
		<td><?php echo h($skill['Skill']['name']); ?>&nbsp;</td>
		<td><?php echo $skill['Type']['name']; ?>&nbsp;</td>
		<td><?php echo h($skill['Skill']['description']); ?>&nbsp;</td>
		<td><?php if($skill['Skill']['cast_time'] == 0.00) { echo 'Instant'; }else{ echo h($skill['Skill']['cast_time']); } ?>&nbsp;</td>
		<td><?php if($skill['Skill']['down_time'] == 0.00) { echo 'None'; }else{ echo h($skill['Skill']['down_time']); } ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View Details'), array('controller' => 'skills', 'action' => 'view', $skill['Skill']['id'])); ?>
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

<?php echo $this->element('side_nav'); ?>