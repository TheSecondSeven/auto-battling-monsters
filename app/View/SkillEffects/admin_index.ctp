<div class="skillEffects index">
	<h2><?php echo __('Effects for ').' '.$skill['Skill']['name']; ?></h2>
	<table cellpadding="0" cellspacing="0">
	<thead>
	<tr>
			<th><?php echo $this->Paginator->sort('skill_effect_id'); ?></th>
			<th>Secondary Effect Of</th>
			<th>Amount</th>
			<th><?php echo $this->Paginator->sort('duration'); ?></th>
			<th><?php echo $this->Paginator->sort('targets'); ?></th>
			<th><?php echo $this->Paginator->sort('chance'); ?></th>
			<th><?php echo $this->Paginator->sort('created'); ?></th>
			<th><?php echo $this->Paginator->sort('modified'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($skillEffects as $skillEffect): ?>
	<tr>
		<td><?php echo h($skillEffect['SkillEffect']['effect']); ?>&nbsp;</td>
		<td>
			<?php if(!empty($skillEffect['PrimarySkillEffect']['id'])) {
				echo $skillEffect['PrimarySkillEffect']['effect'];
			}else{
				echo 'N/A';
			}
			?>
		</td>
		<td><?php if($skillEffect['SkillEffect']['amount_min'] == $skillEffect['SkillEffect']['amount_max']) { echo $skillEffect['SkillEffect']['amount_min']; }else{ echo $skillEffect['SkillEffect']['amount_min'].'-'.$skillEffect['SkillEffect']['amount_max']; } ?>&nbsp;</td>
		<td><?php echo h($skillEffect['SkillEffect']['duration']); ?>&nbsp;</td>
		<td><?php echo h($skillEffect['SkillEffect']['targets']); ?>&nbsp;</td>
		<td><?php echo h($skillEffect['SkillEffect']['chance']); ?>%&nbsp;</td>
		<td><?php echo h($skillEffect['SkillEffect']['created']); ?>&nbsp;</td>
		<td><?php echo h($skillEffect['SkillEffect']['modified']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $skillEffect['SkillEffect']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $skillEffect['SkillEffect']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $skillEffect['SkillEffect']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $skillEffect['SkillEffect']['id']))); ?>
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
		<li><?php echo $this->Html->link(__('New Effect for').' '.$skill['Skill']['name'], array('controller' => 'skill_effects', 'action' => 'add', $skill['Skill']['id'])); ?> </li>
	</ul>
</div>
