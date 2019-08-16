<div class="actions">
	<h3><?php echo __('Shop'); ?></h3>
	<ul>
		<li>Purchase Random<br>Single Type Monster <?php echo $this->Html->link(__('250 Gold'), array('controller' => 'users', 'action' => 'purchase_random_single_type_monster')); ?> </li>
		<br>
		<li>Increase Active<br>Monster Limit<?php echo $this->Html->link(__((50 * $user['User']['active_monster_limit']).' Gems'), array('controller' => 'users', 'action' => 'increase_active_monster_limit')); ?> </li>
	</ul>
	<br>
	<h3><?php echo __('My Stuff'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('My Monsters'), array('controller' => 'users', 'action' => 'my_monsters')); ?> </li>
		<li><?php echo $this->Html->link(__('My Skills'), array('controller' => 'users', 'action' => 'my_skills')); ?> </li>
		<li><?php echo $this->Html->link(__('My Ultimates'), array('controller' => 'users', 'action' => 'my_ultimates')); ?> </li>
		<li><?php echo $this->Html->link(__('My Runes'), array('controller' => 'users', 'action' => 'my_runes')); ?> </li>
		<br>
		<li><?php echo $this->Html->link(__('Completed Gauntlet Runs'), array('controller' => 'gauntlet_runs', 'action' => 'completed')); ?> </li>
		<br>
		<li><?php echo $this->Html->link(__('Leaderboard'), array('controller' => 'monsters', 'action' => 'leaderboard')); ?> </li>
	</ul>
</div>