<?php $this->extend('../layout/dashboard'); ?>
<div class="monsters index">
	<h2><?php echo __('Monsters'); ?></h2>
	<?php if($user->total_gauntlet_runs_today >= $user->active_monster_limit * DAILY_GAUNTLET_LIMIT_PER_ACTIVE_MONSTER) { ?>
	<div class="mb-3">
		You have completed your daily limit of Gauntlet Runs.
		<br>This limit is your "Active Monster Limit" x <?= DAILY_GAUNTLET_LIMIT_PER_ACTIVE_MONSTER ?> and resets at midnight PST.
		<?php if(empty($user->dreaming_since)) { ?><br>Feel free to enter Dream Mode to passively earn gold until you want to start battling again tomorrow.<?php } ?>
	</div>
	<?php } ?>
	<?php if(empty($user->dreaming_since)) { ?>
	<div class="mb-3">
		<?= $this->Html->link('Enter Dream Mode', ['controller' => 'users', 'action' => 'enter-dream-mode'], ['class'=>'btn btn-success', 'data-bs-toggle' => "tooltip", 'data-bs-placement' => "top", 'title' =>'What is Dream Mode? Click the button to read details. There\'s no downside.']); ?>					
	</div>
	<?php }else{ ?>
	<div class="mb-3">
		After 2 hours, your monsters start dreaming of gold for you. Earnings stop after 26 hours.
		<br>Each unused active monster slot will also add chances to earn rune shards or gems!
		<br>Your monsters have been dreaming for <?php $now = new DateTime();
					$past_date = $user->dreaming_since;
					
					$interval = $now->diff($past_date);
					if($interval->d > 0) {
						echo $interval->d.' day'.($interval->d == 1 ? '' : 's');
					}elseif($interval->h > 0) {
						echo $interval->h.' hour'.($interval->h == 1 ? '' : 's');
					}elseif($interval->i > 0) {
						echo $interval->i.' minute'.($interval->i == 1 ? '' : 's');
					}elseif($interval->s > 0) {
						echo $interval->s.' second'.($interval->s == 1 ? '' : 's');
					}else{
						echo '1 second';
					}
					echo '.';
					if($user->dreamt_gold > 0 || $user->dreamt_rune_shards > 0 || $user->dreamt_gems > 0) echo ' They have found';
					if($user->dreamt_gold > 0) echo ' '.$user->dreamt_gold.' gold';
					if($user->dreamt_rune_shards > 0) {
						if($user->dreamt_gold > 0) {
							if($user->dreamt_gems == 0) {
								echo ' and';
							}else{
								echo ',';
							}
						}
						echo ' '.$user->dreamt_rune_shards.' rune shard'.($user->dreamt_rune_shards  == 1 ? '' : 's');
					}
					if($user->dreamt_gems > 0) {
						if($user->dreamt_gold > 0 || $user->dreamt_rune_shards > 0) {
							if($user->dreamt_gold > 0 && $user->dreamt_rune_shards > 0) {
								echo ', and';
							}else{
								echo ' and';
							}
						}
						echo ' '.$user->dreamt_gems.' gem'.($user->dreamt_gems  == 1 ? '' : 's');
					} ?>!
		<br>You can't start new Gauntlet Runs while in Dream Mode.
		<br>
		<?= $this->Html->link('Exit Dream Mode', ['controller' => 'users', 'action' => 'exit-dream-mode'], ['class'=>'btn btn-success']); ?>					
	</div>
	<?php } ?>
	<table  class="table table-striped">
	<thead>
	<tr>
			<th>The Gauntlet</th>
			<th>Name</th>
			<th>Type</th>
			<th>1st Skill</th>
			<th>2nd Skill</th>
			<th>3rd Skill</th>
			<th>4th Skill</th>
			<th>Ultimate</th>
			<th>Battle Rating</th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php 
	foreach ($monsters as $monster): ?>
	<tr>
		<td>
			<?php
			$can_practice = true;
			if($monster->resting_until && (int)$monster->resting_until->toUnixString() > time()) {
				$now = new DateTime();
				$future_date = $monster->resting_until;
				
				$interval = $future_date->diff($now);
				echo 'Resting from Battle for ';
				if($interval->h > 0) {
					echo $interval->h.' hour'.($interval->h == 1 ? '' : 's');
				}elseif($interval->i > 0) {
					echo $interval->i.' minute'.($interval->i == 1 ? '' : 's');
				}elseif($interval->s > 0) {
					echo $interval->s.' second'.($interval->s == 1 ? '' : 's');
				}else{
					echo '1 second';
				}
				echo '.';
			}elseif($monster->in_gauntlet_run) {
				if((int)$monster->in_gauntlet_run_until->toUnixString() <= time()) {
					echo $this->Html->link(__('View Results'), ['controller' => 'gauntlet_runs', 'action' => 'complete-run', $monster->id], ['class' => 'btn btn-success']);
				}else{
					$now = new DateTime();
					$future_date = $monster->in_gauntlet_run_until;
					
					$interval = $future_date->diff($now);
					echo 'Completes the Gauntlet in ';
					if($interval->h > 0) {
						echo $interval->h.' hour'.($interval->h == 1 ? '' : 's');
					}elseif($interval->i > 0) {
						echo $interval->i.' minute'.($interval->i == 1 ? '' : 's');
					}elseif($interval->s > 0) {
						echo $interval->s.' second'.($interval->s == 1 ? '' : 's');
					}else{
						echo '1 second';
					}
					echo '.';
				}
			}elseif(!empty($user->dreaming_since)) {

			}elseif($user->total_gauntlet_runs_today >= $user->active_monster_limit * DAILY_GAUNTLET_LIMIT_PER_ACTIVE_MONSTER) {

			}elseif(empty($monster->skill1->id) || empty($monster->skill2->id) || empty($monster->skill3->id) || empty($monster->skill4->id) || empty($monster->ultimate->id)) {
				echo $this->Html->link(__('Setup abilities to battle in the Gauntlet.'), ['controller' => 'monsters', 'action' => 'edit-move-set', $monster->id], ['class' => 'btn btn-danger']);
				
				$can_practice = false;
			}else{
				if($battle_available) {
					echo $this->Html->link(__('Battle in the Gauntlet!'), array('controller' => 'gauntlet-runs', 'action' => 'start-run', $monster->id), ['class' => 'btn btn-primary']);
				}else{
					echo 'You can only have '.$user->active_monster_limit.' Monster'.($user->active_monster_limit == 1 ? '' : 's').' active in the Gauntlet at a time.';
				}
			}
			?>
		</td>
		<td><?php echo $monster->name; ?>&nbsp;</td>
		<td><?php echo $monster->type->name; if(!empty($monster->secondary_type->id)) echo '/'.$monster->secondary_type->name; ?>&nbsp;</td>
		<td>
			<?php 
				if(!empty($monster->skill1->id)) {
					echo $this->Html->link($monster->skill1->name, array('controller' => 'skills', 'action' => 'view', $monster->skill1->id), ['class' => 'btn btn-secondary']);
				}else{
					echo 'No Skill Set';
				}
			?>
		</td>
		<td>
			<?php 
				if(!empty($monster->skill2->id)) {
					echo $this->Html->link($monster->skill2->name, array('controller' => 'skills', 'action' => 'view', $monster->skill2->id), ['class' => 'btn btn-secondary']);
				}else{
					echo 'No Skill Set';
				}
			?>
		</td>
		<td>
			<?php 
				if(!empty($monster->skill3->id)) {
					echo $this->Html->link($monster->skill3->name, array('controller' => 'skills', 'action' => 'view', $monster->skill3->id), ['class' => 'btn btn-secondary']);
				}else{
					echo 'No Skill Set';
				}
			?>
		</td>
		<td>
			<?php 
				if(!empty($monster->skill4->id)) {
					echo $this->Html->link($monster->skill4->name, array('controller' => 'skills', 'action' => 'view', $monster->skill4->id), ['class' => 'btn btn-secondary']);
				}else{
					echo 'No Skill Set';
				}
			?>
		</td>
		<td>
			<?php 
				if(!empty($monster->ultimate->id)) {
					echo $this->Html->link($monster->ultimate->name, array('controller' => 'ultimates', 'action' => 'view', $monster->ultimate->id), ['class' => 'btn btn-secondary']);
				}else{
					echo 'No Ultimate Set';
				}
			?>
		</td>
		<td><?php echo $monster->elo_rating; ?>&nbsp;</td>
		<td class="dropdown">
			<div class="dropdown">
				<button class="btn btn-primary" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
					<?php echo $this->Html->icon('pencil-fill'); ?>
				</button>
				<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
					<?php if($monster->in_gauntlet_run) {
								echo '<li><span class="dropdown-item">You cannot edit your monster<br>while it is in the Gauntlet.</span></li>';
							}elseif($monster->resting_until && (int)$monster->resting_until->toUnixString() > time()) {
								echo '<li><span class="dropdown-item">You cannot edit your monster<br>while it is resting from Battle.</span></li>';
							}else{
								echo $this->Html->link(__('Edit Name'), ['controller' => 'monsters', 'action' => 'edit', $monster->id], ['class'=>'dropdown-item']);
								echo $this->Html->link(__('Edit Move Set'), ['controller' => 'monsters', 'action' => 'edit-move-set', $monster->id], ['class'=>'dropdown-item']);
								echo $this->Html->link(__('Edit Runes'), ['controller' => 'monsters', 'action' => 'edit-runes', $monster->id], ['class'=>'dropdown-item']);
							}
							
							if($can_practice) {
								echo $this->Html->link('Practice', ['controller' => 'battle', 'action' => 'practice', $monster->id], ['class'=>'dropdown-item']);
							} ?>
				</ul>
			</div>
		</td>
	</tr>
<?php endforeach; ?>
	</tbody>
	</table>
</div>

