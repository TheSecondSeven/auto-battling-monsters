<div class="users view">
<h2><?php echo __('User'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($user['User']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Username'); ?></dt>
		<dd>
			<?php echo h($user['User']['username']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Password'); ?></dt>
		<dd>
			<?php echo h($user['User']['password']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($user['User']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($user['User']['modified']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit User'), array('action' => 'edit', $user['User']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete User'), array('action' => 'delete', $user['User']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $user['User']['id']))); ?> </li>
		<li><?php echo $this->Html->link(__('List Users'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Monsters'), array('controller' => 'monsters', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Monster'), array('controller' => 'monsters', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php echo __('Related Monsters'); ?></h3>
	<?php if (!empty($user['Monster'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('User Id'); ?></th>
		<th><?php echo __('Name'); ?></th>
		<th><?php echo __('Type Id'); ?></th>
		<th><?php echo __('Strength'); ?></th>
		<th><?php echo __('Agility'); ?></th>
		<th><?php echo __('Dexterity'); ?></th>
		<th><?php echo __('Intelligence'); ?></th>
		<th><?php echo __('Luck'); ?></th>
		<th><?php echo __('Vitality'); ?></th>
		<th><?php echo __('Skill 1 Id'); ?></th>
		<th><?php echo __('Skill 2 Id'); ?></th>
		<th><?php echo __('Skill 3 Id'); ?></th>
		<th><?php echo __('Skill 4 Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($user['Monster'] as $monster): ?>
		<tr>
			<td><?php echo $monster['id']; ?></td>
			<td><?php echo $monster['user_id']; ?></td>
			<td><?php echo $monster['name']; ?></td>
			<td><?php echo $monster['type_id']; ?></td>
			<td><?php echo $monster['strength']; ?></td>
			<td><?php echo $monster['agility']; ?></td>
			<td><?php echo $monster['dexterity']; ?></td>
			<td><?php echo $monster['intelligence']; ?></td>
			<td><?php echo $monster['luck']; ?></td>
			<td><?php echo $monster['vitality']; ?></td>
			<td><?php echo $monster['skill_1_id']; ?></td>
			<td><?php echo $monster['skill_2_id']; ?></td>
			<td><?php echo $monster['skill_3_id']; ?></td>
			<td><?php echo $monster['skill_4_id']; ?></td>
			<td><?php echo $monster['created']; ?></td>
			<td><?php echo $monster['modified']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'monsters', 'action' => 'view', $monster['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'monsters', 'action' => 'edit', $monster['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'monsters', 'action' => 'delete', $monster['id']), array('confirm' => __('Are you sure you want to delete # %s?', $monster['id']))); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Monster'), array('controller' => 'monsters', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
