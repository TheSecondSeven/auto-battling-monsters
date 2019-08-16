<div class="skills view">
<h2><?php echo __('Skill'); ?></h2>
	<dl>
		<dt><?php echo __('Rarity'); ?></dt>
		<dd>
			<?php echo h($skill['Skill']['rarity']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Value'); ?></dt>
		<dd>
			<?php echo h($skill['Skill']['value']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($skill['Skill']['name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Type'); ?></dt>
		<dd>
			<?php echo $this->Html->link($skill['Type']['name'], array('controller' => 'types', 'action' => 'view', $skill['Type']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Description'); ?></dt>
		<dd>
			<?php echo h($skill['Skill']['description']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Cast Time'); ?></dt>
		<dd>
			<?php echo h($skill['Skill']['cast_time']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Down Time'); ?></dt>
		<dd>
			<?php echo h($skill['Skill']['down_time']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Skill'), array('controller' => 'skill_effects', 'action' => 'edit_skill', $skill['Skill']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Skill'), array('action' => 'delete', $skill['Skill']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $skill['Skill']['id']))); ?> </li>
		<li><?php echo $this->Html->link(__('List Skills'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Skill'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Types'), array('controller' => 'types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Type'), array('controller' => 'types', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php echo __('Effects'); ?></h3>
	<?php if (!empty($skill['SkillEffect'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th colspan="2"><?php echo __('Effect'); ?></th>
		<th><?php echo __('Chance'); ?></th>
		<th><?php echo __('Amount'); ?></th>
		<th><?php echo __('Targets'); ?></th>
		<th><?php echo __('Duration'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($skill['SkillEffect'] as $skillEffect):
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
			<li><?php echo $this->Html->link(__('New Effect for ').$skill['Skill']['name'], array('controller' => 'skill_effects', 'action' => 'add', $skill['Skill']['id'])); ?> </li>
		</ul>
	</div>
</div>
