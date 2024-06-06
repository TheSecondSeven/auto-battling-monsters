<?php $this->extend('../layout/dashboard'); ?>
<div class="monsters index">
	<h2><?php echo __('Monsters'); ?></h2>
	<table  class="table table-striped">
	<thead>
	<tr>
			<th style="text-align: left;">Status</th>
			<th>Name</th>
			<th>Type</th>
			<th>Move Set</th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php 
	foreach ($monsters as $monster): ?>
	<tr style="vertical-align:middle;">
		<td style="text-align: left;">
			<?php
			$can_practice = true;
			if($monster->resting_until && (int)$monster->resting_until->toUnixString() > time()) {
				echo '<div class="alert alert-warning alert-label" role="alert">';
				$now = new DateTime();
				$future_date = $monster->resting_until;
				
				$interval = $future_date->diff($now);
				echo 'Resting from Battle<br>for ';
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
				echo '</div>';
			}elseif($monster->in_gauntlet_run) {
				if((int)$monster->in_gauntlet_run_until->toUnixString() <= time()) {
					echo '<div class="alert alert-success alert-label" role="alert">Completed Gauntlet Run!</div>';
				}else{
					echo '<div class="alert alert-info alert-label" role="alert">';
					$now = new DateTime();
					$future_date = $monster->in_gauntlet_run_until;
					
					$interval = $future_date->diff($now);
					echo 'Running the Gauntlet<br>for ';
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
					echo '</div>';
				}
			}elseif(empty($monster->skill1->id) || empty($monster->skill2->id) || empty($monster->skill3->id) || empty($monster->skill4->id) || empty($monster->ultimate->id)) {
				$can_practice = false;
				echo '<div class="alert alert-danger alert-label" role="alert">Moves Not Set</div>';
			}else{
				echo '<div class="alert alert-primary alert-label" role="alert">Available</div>';
			}
			?>
		</td>
		<td><?= $monster->name ?></td>
		<td><?= $monster->get('type_verbose') ?></td>
		<td><?= $monster->get('moveset') ?></td>
		
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

