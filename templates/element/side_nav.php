<div class="actions">
	<h1 class="display-6">Shop</h1>
	<ul class="list-group">
		<li class="list-group-item">
			Purchase Random<br>Single Type Monster</br>
			<?php echo $this->Html->link(SINGLE_TYPE_MONSTER_COST.' Gold', ['controller' => 'users', 'action' => 'purchase-random-single-type-monster'], ['class' => 'btn btn-primary']); ?>
		</li>
		<li class="list-group-item">
			Purchase Random<br>Dual Type Monster</br>
			<?php echo $this->Html->link(DUAL_TYPE_MONSTER_COST.' Gold', ['controller' => 'users', 'action' => 'purchase-random-dual-type-monster'], ['class' => 'btn btn-primary']); ?>
		</li>
		<br>
		<li class="list-group-item">
			Increase Active<br>Monster Limit
			<br>
			<?php echo $this->Html->link((50 * $user->active_monster_limit).' Gems', ['controller' => 'users', 'action' => 'increase-active-monster-limit'], ['class' => 'btn btn-primary']); ?> </li>
	</ul>
	<br>
	<h1 class="display-6">My Stuff</h1>
	<div class="list-group">
		<?= $this->Html->link('My Monsters', ['controller' => 'monsters', 'action' => 'my-monsters'], ['class'=>'list-group-item list-group-item-action']); ?>
		<?= $this->Html->link('My Skills', ['controller' => 'skills', 'action' => 'my-skills'], ['class'=>'list-group-item list-group-item-action']); ?> </li>
		<?= $this->Html->link('My Ultimates', ['controller' => 'ultimates', 'action' => 'my-ultimates'], ['class'=>'list-group-item list-group-item-action']); ?> </li>
		<?= $this->Html->link('My Runes', ['controller' => 'runes', 'action' => 'my-runes'], ['class'=>'list-group-item list-group-item-action']); ?> </li>
		<br>
		<?= $this->Html->link('Completed Gauntlet Runs', ['controller' => 'gauntlet-runs', 'action' => 'completed'], ['class'=>'list-group-item list-group-item-action']); ?> </li>
		<br>
		<?= $this->Html->link('Leaderboard', ['controller' => 'monsters', 'action' => 'leaderboard'], ['class'=>'list-group-item list-group-item-action']); ?> </li>
		<?php if($user->role == 'Admin') { ?>
		<br>
		<?= $this->Html->link('Leaderboard', ['controller' => 'monsters', 'action' => 'leaderboard'], ['class'=>'list-group-item list-group-item-action']); ?> </li>
		<?php } ?>
	</ul>
</div>