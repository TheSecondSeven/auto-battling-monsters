<div class="augments index">
	<h2><?php echo __('Augments'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<thead>
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('rarity'); ?></th>
			<th><?php echo $this->Paginator->sort('name'); ?></th>
			<th><?php echo $this->Paginator->sort('skill_1'); ?></th>
			<th><?php echo $this->Paginator->sort('skill_2'); ?></th>
			<th><?php echo $this->Paginator->sort('skill_3'); ?></th>
			<th><?php echo $this->Paginator->sort('skill_4'); ?></th>
			<th><?php echo $this->Paginator->sort('ultimate'); ?></th>
			<th><?php echo $this->Paginator->sort('type'); ?></th>
			<th><?php echo $this->Paginator->sort('amount_1'); ?></th>
			<th><?php echo $this->Paginator->sort('amount_2'); ?></th>
			<th><?php echo $this->Paginator->sort('amount_3'); ?></th>
			<th><?php echo $this->Paginator->sort('created'); ?></th>
			<th><?php echo $this->Paginator->sort('modified'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($augments as $augment): ?>
	<tr>
		<td><?php echo h($augment['Augment']['id']); ?>&nbsp;</td>
		<td><?php echo h($augment['Augment']['rarity']); ?>&nbsp;</td>
		<td><?php echo h($augment['Augment']['name']); ?>&nbsp;</td>
		<td><?php echo h($augment['Augment']['skill_1']); ?>&nbsp;</td>
		<td><?php echo h($augment['Augment']['skill_2']); ?>&nbsp;</td>
		<td><?php echo h($augment['Augment']['skill_3']); ?>&nbsp;</td>
		<td><?php echo h($augment['Augment']['skill_4']); ?>&nbsp;</td>
		<td><?php echo h($augment['Augment']['ultimate']); ?>&nbsp;</td>
		<td><?php echo h($augment['Augment']['type']); ?>&nbsp;</td>
		<td><?php echo h($augment['Augment']['amount_1']); ?>&nbsp;</td>
		<td><?php echo h($augment['Augment']['amount_2']); ?>&nbsp;</td>
		<td><?php echo h($augment['Augment']['amount_3']); ?>&nbsp;</td>
		<td><?php echo h($augment['Augment']['created']); ?>&nbsp;</td>
		<td><?php echo h($augment['Augment']['modified']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $augment['Augment']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $augment['Augment']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $augment['Augment']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $augment['Augment']['id']))); ?>
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
		<li><?php echo $this->Html->link(__('New Augment'), array('action' => 'add')); ?></li>
	</ul>
</div>
