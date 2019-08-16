<div class="ultimates view">
<h2><?php echo __('Ultimate'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($ultimate['Ultimate']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Type'); ?></dt>
		<dd>
			<?php echo $this->Html->link($ultimate['Type']['name'], array('controller' => 'types', 'action' => 'view', $ultimate['Type']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Secondary Type'); ?></dt>
		<dd>
			<?php echo $this->Html->link($ultimate['SecondaryType']['name'], array('controller' => 'types', 'action' => 'view', $ultimate['SecondaryType']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($ultimate['Ultimate']['name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Description'); ?></dt>
		<dd>
			<?php echo h($ultimate['Ultimate']['description']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Charges Needed'); ?></dt>
		<dd>
			<?php echo h($ultimate['Ultimate']['charges_needed']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Starting Charges'); ?></dt>
		<dd>
			<?php echo h($ultimate['Ultimate']['starting_charges']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Cast Time'); ?></dt>
		<dd>
			<?php echo h($ultimate['Ultimate']['cast_time']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Down Time'); ?></dt>
		<dd>
			<?php echo h($ultimate['Ultimate']['down_time']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($ultimate['Ultimate']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($ultimate['Ultimate']['modified']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Ultimate'), array('action' => 'edit', $ultimate['Ultimate']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Ultimate'), array('action' => 'delete', $ultimate['Ultimate']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $ultimate['Ultimate']['id']))); ?> </li>
		<li><?php echo $this->Html->link(__('List Ultimates'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Ultimate'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Types'), array('controller' => 'types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Type'), array('controller' => 'types', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Skill Effects'), array('controller' => 'skill_effects', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Skill Effect'), array('controller' => 'skill_effects', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php echo __('Effects'); ?></h3>
	<?php if (!empty($ultimate['SkillEffect'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th colspan="2"><?php echo __('Effect'); ?></th>
		<th><?php echo __('Chance'); ?></th>
		<th><?php echo __('Amount'); ?></th>
		<th><?php echo __('Targets'); ?></th>
		<th><?php echo __('Duration'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($ultimate['SkillEffect'] as $skillEffect):
		if($skillEffect['effect'] != 'Random Amount' || empty($skillEffect['SecondarySkillEffect'])) {
	?>
		<tr>
			<td colspan="2"><?php echo $skillEffect['effect']; if($skillEffect['effect'] == 'Consume') { echo ' Status: '.$status_options[$skillEffect['status']]; } ?></td>
			<td><?php echo $skillEffect['chance']; ?>%</td>
			<td><?php if($skillEffect['amount_min'] == $skillEffect['amount_max']) { echo $skillEffect['amount_min']; }else{ echo $skillEffect['amount_min'].'-'.$skillEffect['amount_max']; } ?></td>
			<td><?php echo $skillEffect['targets']; ?></td>
			<td><?php if($skillEffect['duration'] == 0) { echo 'N/A'; }else{ echo $skillEffect['duration']; } ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'skill_effects', 'action' => 'edit', $skillEffect['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'skill_effects', 'action' => 'delete', $skillEffect['id']), array('confirm' => __('Are you sure you want to delete # %s?', $skillEffect['id']))); ?>
			</td>
		</tr>
	<?php 
		} //end not random amount
		if(!empty($skillEffect['SecondarySkillEffect'])) {
			if($skillEffect['effect'] == 'Random Amount') {
				echo '<tr><td colspan="6">Will do the following '.$skillEffect['amount_min'].' - '.$skillEffect['amount_max'].' times:</td>';
				echo '<td class="actions">'.$this->Html->link(__('Edit'), array('controller' => 'skill_effects', 'action' => 'edit', $skillEffect['id'])).'</td></tr>';
			}elseif($skillEffect['effect'] == 'Consume') {
				echo '<tr><td></td><td colspan="6">For each stack consumed:</td></tr>';
			}else{
				echo '<tr><td></td><td colspan="6">If this Effect succeeds it will trigger:</td></tr>';
			}
		}
		foreach($skillEffect['SecondarySkillEffect'] as $secondarySkillEffect) { ?>
			<tr>
				<td></td>
				<td><?php echo $secondarySkillEffect['effect']; ?></td>
				<td><?php echo $secondarySkillEffect['chance']; ?>%</td>
				<td><?php if($secondarySkillEffect['amount_min'] == $secondarySkillEffect['amount_max']) { echo $secondarySkillEffect['amount_min']; }else{ echo $secondarySkillEffect['amount_min'].'-'.$secondarySkillEffect['amount_max']; } ?></td>
				<td><?php echo $secondarySkillEffect['targets']; ?></td>
				<td><?php if($secondarySkillEffect['duration'] == 0) { echo 'N/A'; }else{ echo $secondarySkillEffect['duration']; } ?></td>
				<td class="actions">
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'skill_effects', 'action' => 'edit', $secondarySkillEffect['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'skill_effects', 'action' => 'delete', $secondarySkillEffect['id']), array('confirm' => __('Are you sure you want to delete # %s?', $secondarySkillEffect['id']))); ?>
			</td>
			</tr>
		<?php
		}
		endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Effect'), array('controller' => 'ultimates', 'action' => 'add_skill_effect', $ultimate['Ultimate']['id'])); ?> </li>
		</ul>
	</div>
</div>
