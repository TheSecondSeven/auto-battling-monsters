<div class="skills index">
	<h2><?php echo __('My Runes'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<?php 
	if(empty($augments)) {
		echo '<tr><td colspan="8">You have not unlocked any Runes yet.</td></tr>';
	}else{ ?>	
	<thead>
	<tr>
		<th><?php echo $this->Paginator->sort('rarity'); ?></th>
		<th><?php echo $this->Paginator->sort('type'); ?></th>
		<th><?php echo $this->Paginator->sort('name'); ?></th>
		<th><?php echo $this->Paginator->sort('description'); ?></th>
		<th><?php echo $this->Paginator->sort('skill_1', '1st Skill'); ?></th>
		<th><?php echo $this->Paginator->sort('skill_2', '2nd Skill'); ?></th>
		<th><?php echo $this->Paginator->sort('skill_3', '3rd Skill'); ?></th>
		<th><?php echo $this->Paginator->sort('skill_4', '4th Skill'); ?></th>
		<th><?php echo $this->Paginator->sort('ultimate'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($augments as $augment): ?>
	<tr>
		<td><?php echo h($augment['Augment']['rarity']); ?>&nbsp;</td>
		<td><?php if($augment['Augment']['type'] == 'Type') {
			echo 'Unlock Type';
		}elseif($augment['Augment']['type'] == 'Damage') {
			echo 'Damage Increase';
		}elseif($augment['Augment']['type'] == 'Healing') {
			echo 'Healing Increase';
		}elseif($augment['Augment']['type'] == 'Chance To Cast Again') {
			echo 'Recast';
		}
			; ?>&nbsp;</td>
		<td><?php echo h($augment['Augment']['name']); ?>&nbsp;</td>
		<td><?php echo h($augment['Augment']['description']); ?>&nbsp;</td>
		<td><?php if($augment['Augment']['skill_1']) { echo '✓'; }else{ echo ''; } ?>&nbsp;</td>
		<td><?php if($augment['Augment']['skill_2']) { echo '✓'; }else{ echo ''; } ?>&nbsp;</td>
		<td><?php if($augment['Augment']['skill_3']) { echo '✓'; }else{ echo ''; } ?>&nbsp;</td>
		<td><?php if($augment['Augment']['skill_4']) { echo '✓'; }else{ echo ''; } ?>&nbsp;</td>
		<td><?php if($augment['Augment']['ultimate']) { echo '✓'; }else{ echo ''; } ?>&nbsp;</td>
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