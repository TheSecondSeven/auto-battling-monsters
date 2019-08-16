<div class="skillEffects view">
<h2><?php echo __('Skill Effect'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($skillEffect['SkillEffect']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Skill'); ?></dt>
		<dd>
			<?php echo $this->Html->link($skillEffect['Skill']['name'], array('controller' => 'skills', 'action' => 'view', $skillEffect['Skill']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Skill Effect'); ?></dt>
		<dd>
			<?php echo $this->Html->link($skillEffect['SkillEffect']['effect'], array('controller' => 'skill_effects', 'action' => 'view', $skillEffect['SkillEffect']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Effect'); ?></dt>
		<dd>
			<?php echo h($skillEffect['SkillEffect']['effect']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Amount'); ?></dt>
		<dd>
			<?php echo h($skillEffect['SkillEffect']['amount']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Duration'); ?></dt>
		<dd>
			<?php echo h($skillEffect['SkillEffect']['duration']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Targets'); ?></dt>
		<dd>
			<?php echo h($skillEffect['SkillEffect']['targets']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Chance'); ?></dt>
		<dd>
			<?php echo h($skillEffect['SkillEffect']['chance']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($skillEffect['SkillEffect']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($skillEffect['SkillEffect']['modified']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Skill Effect'), array('action' => 'edit', $skillEffect['SkillEffect']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Skill Effect'), array('action' => 'delete', $skillEffect['SkillEffect']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $skillEffect['SkillEffect']['id']))); ?> </li>
		<li><?php echo $this->Html->link(__('List Skill Effects'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Skill Effect'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Skills'), array('controller' => 'skills', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Skill'), array('controller' => 'skills', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Skill Effects'), array('controller' => 'skill_effects', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Skill Effect'), array('controller' => 'skill_effects', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php echo __('Related Skill Effects'); ?></h3>
	<?php if (!empty($skillEffect['SkillEffect'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Skill Id'); ?></th>
		<th><?php echo __('Skill Effect Id'); ?></th>
		<th><?php echo __('Effect'); ?></th>
		<th><?php echo __('Amount'); ?></th>
		<th><?php echo __('Duration'); ?></th>
		<th><?php echo __('Targets'); ?></th>
		<th><?php echo __('Chance'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($skillEffect['SkillEffect'] as $skillEffect): ?>
		<tr>
			<td><?php echo $skillEffect['id']; ?></td>
			<td><?php echo $skillEffect['skill_id']; ?></td>
			<td><?php echo $skillEffect['skill_effect_id']; ?></td>
			<td><?php echo $skillEffect['effect']; ?></td>
			<td><?php echo $skillEffect['amount']; ?></td>
			<td><?php echo $skillEffect['duration']; ?></td>
			<td><?php echo $skillEffect['targets']; ?></td>
			<td><?php echo $skillEffect['chance']; ?></td>
			<td><?php echo $skillEffect['created']; ?></td>
			<td><?php echo $skillEffect['modified']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'skill_effects', 'action' => 'view', $skillEffect['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'skill_effects', 'action' => 'edit', $skillEffect['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'skill_effects', 'action' => 'delete', $skillEffect['id']), array('confirm' => __('Are you sure you want to delete # %s?', $skillEffect['id']))); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Skill Effect'), array('controller' => 'skill_effects', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
