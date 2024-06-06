<div class="actions">
	
	<div class="list-group">
		<?= $this->Html->link('Campaign', ['controller' => 'quests', 'action' => 'index'], ['class'=>'list-group-item list-group-item-action']); ?>
		<?= $this->Html->link('The Gauntlet'.(!empty($user->gauntlet_runs_to_be_completed) ? ' ('.$user->gauntlet_runs_to_be_completed.')' : ''), ['controller' => 'gauntlet_runs', 'action' => 'index'], ['class'=>'list-group-item list-group-item-action'.(!empty($user->gauntlet_runs_to_be_completed) ? ' list-group-item-success' : '')]); ?>
		<!--<?= $this->Html->link('Completed Gauntlet Runs', ['controller' => 'gauntlet-runs', 'action' => 'completed'], ['class'=>'list-group-item list-group-item-action']); ?>-->
	</div>
	<br>
	<div class="list-group">
		<?= $this->Html->link('My Monsters'.(!empty($user->available_monsters) ? ' ('.$user->available_monsters.')' : ''), ['controller' => 'monsters', 'action' => 'my-monsters'], ['class'=>'list-group-item list-group-item-action'.(!empty($user->available_monsters) ? ' list-group-item-primary' : '')]); ?>
		<?= $this->Html->link('My Skills', ['controller' => 'skills', 'action' => 'my-skills'], ['class'=>'list-group-item list-group-item-action']); ?>
		<?= $this->Html->link('My Ultimates', ['controller' => 'ultimates', 'action' => 'my-ultimates'], ['class'=>'list-group-item list-group-item-action']); ?>
		<?= $this->Html->link('My Runes', ['controller' => 'runes', 'action' => 'my-runes'], ['class'=>'list-group-item list-group-item-action']); ?>
	</div>
	<br>

	<div class="list-group">
		<?= $this->Html->link('Skills', ['controller' => 'skills', 'action' => 'index'], ['class'=>'list-group-item list-group-item-action']); ?>
		<?= $this->Html->link('Ultimates', ['controller' => 'ultimates', 'action' => 'index'], ['class'=>'list-group-item list-group-item-action']); ?>
		<?= $this->Html->link('Statuses', ['controller' => 'statuses', 'action' => 'index'], ['class'=>'list-group-item list-group-item-action']); ?>
		<?= $this->Html->link('Gauntlet Leaderboard', ['controller' => 'monsters', 'action' => 'leaderboard'], ['class'=>'list-group-item list-group-item-action']); ?>
	</div>
	<br>
	<ul class="list-group">
		<li class="list-group-item">
			Purchase Random<br>Single Type Monster</br>
			<?php echo $this->Html->link(SINGLE_TYPE_MONSTER_COST.' Gold', ['controller' => 'users', 'action' => 'purchase-random-single-type-monster'], ['class' => 'btn btn-primary']); ?>
		</li>
		<li class="list-group-item">
			Purchase Random<br>Dual Type Monster</br>
			<?php echo $this->Html->link(DUAL_TYPE_MONSTER_COST.' Gold', ['controller' => 'users', 'action' => 'purchase-random-dual-type-monster'], ['class' => 'btn btn-primary']); ?>
		</li>
	</ul>
</div>