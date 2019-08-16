<div class="skills index">
	<h2><?php echo __('My Ultimates'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<thead>
	<tr>
		<th><?php echo $this->Paginator->sort('rarity'); ?></th>
		<th><?php echo $this->Paginator->sort('name'); ?></th>
		<th><?php echo $this->Paginator->sort('type_id'); ?></th>
		<th><?php echo $this->Paginator->sort('description'); ?></th>
		<th><?php echo $this->Paginator->sort('passive'); ?></th>
		<th><?php echo $this->Paginator->sort('cast_time'); ?></th>
		<th><?php echo $this->Paginator->sort('down_time'); ?></th>
		<th class="actions"></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($ultimates as $ultimate): ?>
	<tr>
		<td><?php echo h($ultimate['Ultimate']['rarity']); ?>&nbsp;</td>
		<td><?php echo h($ultimate['Ultimate']['name']); ?>&nbsp;</td>
		<td><?php echo $ultimate['Type']['name'].'/'.$ultimate['SecondaryType']['name']; ?>&nbsp;</td>
		<td><?php echo h($ultimate['Ultimate']['description']); ?>&nbsp;</td>
		<td><?php if($ultimate['Ultimate']['passive']) { echo 'Yes'; }else{ echo 'No'; } ?>&nbsp;</td>
		<td><?php if($ultimate['Ultimate']['passive']) { echo 'N/A'; }else{ echo h($ultimate['Ultimate']['cast_time']); } ?>&nbsp;</td>
		<td><?php if($ultimate['Ultimate']['passive']) { echo 'N/A'; }else{ echo h($ultimate['Ultimate']['down_time']); } ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View Details'), array('controller' => 'ultimates', 'action' => 'view', $ultimate['Ultimate']['id'])); ?>
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