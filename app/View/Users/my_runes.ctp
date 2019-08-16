<div class="skills index">
	<h2><?php echo __('My Runes'); ?></h2>
	<?php echo $this->Html->link(__('Create Rune'), array('controller' => 'runes', 'action' => 'create')); ?>
	<table cellpadding="0" cellspacing="0">
	<?php 
	if(empty($runes)) {
		echo '<tr><td colspan="10">You have not created any Runes yet.</td></tr>';
	}else{ ?>	
	<thead>
	<tr>
		<th><?php echo $this->Paginator->sort('type_id'); ?></th>
		<th><?php echo $this->Paginator->sort('level'); ?></th>
		<th><?php echo $this->Paginator->sort('in_use_by_monster_id', 'Used By'); ?></th>
		<th><?php echo $this->Paginator->sort('unlock_type', 'Unlock Type'); ?></th>
		<th><?php echo $this->Paginator->sort('damage_level', 'Damage Increase'); ?></th>
		<th><?php echo $this->Paginator->sort('healing_level', 'Healing Increase'); ?></th>
		<th><?php echo $this->Paginator->sort('critical_chance_level', 'Critical Chance Increase'); ?></th>
		<th><?php echo $this->Paginator->sort('cast_again_level', 'Overload Chance Increase'); ?></th>
		<th><?php echo $this->Paginator->sort('casting_speed_level', 'Cast Speed Increase'); ?></th>
		<th><?php echo $this->Paginator->sort('health_level', 'Health Increase'); ?></th>
		<th>Actions</th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($runes as $rune): ?>
	<tr>
		<td><?php echo h($rune['Type']['name']); ?>&nbsp;</td>
		<td><?php echo h($rune['Rune']['level']); ?>&nbsp;</td>
		<td><?php if(!empty($rune['Monster']['id'])) { echo $rune['Monster']['name']; }else{ echo 'Available'; } ?>&nbsp;</td>
		<td><?php if($rune['Rune']['unlock_type']) { echo 'Unlocks '.$rune['Type']['name'].' Skills'; }else{ echo 'No'; } ?></td>
		<td><?php if($rune['Rune']['damage_level'] > 0) { echo $rune['Type']['name'].' Damage Increased '.(RUNE_DAMAGE_INCREASE * $rune['Rune']['damage_level']).'%'; }else{ echo 'None'; } ?>&nbsp;</td>
		<td><?php if($rune['Rune']['healing_level'] > 0) { echo $rune['Type']['name'].' Healing Increased '.(RUNE_HEALING_INCREASE * $rune['Rune']['healing_level']).'%'; }else{ echo 'None'; } ?>&nbsp;</td>
		<td><?php if($rune['Rune']['critical_chance_level'] > 0) { echo 'Critical Hits with '.$rune['Type']['name'].' Abilities Increased '.(RUNE_CRITICAL_CHANCE_INCREASE * $rune['Rune']['critical_chance_level']).'%'; }else{ echo 'None'; } ?>&nbsp;</td>
		<td><?php if($rune['Rune']['cast_again_level'] > 0) { echo $rune['Type']['name'].' Skills Have a '.(RUNE_CAST_AGAIN_INCREASE * $rune['Rune']['cast_again_level']).'% to Cast Again'; }else{ echo 'None'; } ?>&nbsp;</td>
		<td><?php if($rune['Rune']['casting_speed_level'] > 0) { echo 'Casting Speed of '.$rune['Type']['name'].' Skills Increased '.(RUNE_CASTING_SPEED_INCREASE * $rune['Rune']['casting_speed_level']).'%'; }else{ echo 'None'; } ?>&nbsp;</td>
		<td><?php if($rune['Rune']['health_level'] > 0) { echo 'Monster Health Increased '.(RUNE_HEALTH_INCREASE * $rune['Rune']['health_level']).'%'; }else{ echo 'None'; } ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('Upgrade'), array('controller' => 'runes', 'action' => 'upgrade', $rune['Rune']['id'])); ?>
		</td>
	</tr>
<?php endforeach;
	} ?>
	</tbody>
	</table>
	<?php 
	if(!empty($augments)) { ?>
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
	<?php } ?>
</div>

<?php echo $this->element('side_nav'); ?>