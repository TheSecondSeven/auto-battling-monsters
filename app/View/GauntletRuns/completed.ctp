<div class="monsters index">
	<h2><?php echo __('Completed Gauntlet Runs'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<thead>
	<tr>
			<th><?php echo $this->Paginator->sort('monster_id'); ?></th>
			<th style="text-align: center;"><?php echo $this->Paginator->sort('wins'); ?></th>
			<th style="text-align: center;"><?php echo $this->Paginator->sort('losses'); ?></th>
			<th style="text-align: center;"><?php echo $this->Paginator->sort('ties'); ?></th>
			<th style="text-align: center;"><?php echo $this->Paginator->sort('created', 'Completed'); ?></th>
			<th class="actions"></th>
	</tr>
	</thead>
	<tbody>
	<?php 
		foreach ($gauntlet_runs as $gauntlet_run): ?>
	<tr>
		<td><?php echo $gauntlet_run['Monster']['name']; ?></td>
		<td style="text-align: center;"><?php echo $gauntlet_run['GauntletRun']['wins']; ?>&nbsp;</td>
		<td style="text-align: center;"><?php echo $gauntlet_run['GauntletRun']['losses']; ?>&nbsp;</td>
		<td style="text-align: center;"><?php echo $gauntlet_run['GauntletRun']['ties']; ?>&nbsp;</td>
		<td style="text-align: center;"><?php echo date('F jS, Y g:ia', strtotime($gauntlet_run['GauntletRun']['created'])); ?></td>
		<td class="actions">
			<?php echo $this->Html->link(__('View Battles'), array('controller' => 'gauntlet_runs', 'action' => 'view_battles', $gauntlet_run['GauntletRun']['id'], 'admin' => false)); ?>
			<?php echo $this->Html->link(__('View Results'), array('controller' => 'gauntlet_runs', 'action' => 'view_results', $gauntlet_run['GauntletRun']['id'], 'admin' => false)); ?>
		</td>
	</tr>
	<tr>
		<td colspan="7"><?php if(!empty($gauntlet_run['Skill1']['id'])) { 
			echo '<span style="display:inline-block;width:20%;text-align:center;">'.$gauntlet_run['Skill1']['name'];
			echo '</span>';
			echo '<span style="display:inline-block;width:20%;text-align:center;">'.$gauntlet_run['Skill2']['name'];
			echo '</span>';
			echo '<span style="display:inline-block;width:20%;text-align:center;">'.$gauntlet_run['Skill3']['name'];
			echo '</span>';
			echo '<span style="display:inline-block;width:20%;text-align:center;">'.$gauntlet_run['Skill4']['name'];
			echo '</span>';
			echo '<span style="display:inline-block;width:20%;text-align:center;">'.$gauntlet_run['Ultimate']['name'];
			echo '</span>';
		} ?>
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
