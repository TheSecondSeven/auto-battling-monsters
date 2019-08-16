<div class="monsters index">
	<h2><?php echo __('Monsters'); ?></h2>
	<table cellpadding="0" cellspacing="0">
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
			if(empty($monster['Skill1']['id']) || empty($monster['Skill2']['id']) || empty($monster['Skill3']['id']) || empty($monster['Skill4']['id']) || empty($monster['Ultimate']['id'])) {
				echo '<span style="color:#c43c35;">Setup abilities to battle in the Gauntlet.</span>';
				$can_practice = false;
			}elseif($monster['Monster']['in_gauntlet_run']) {
				if(strtotime($monster['Monster']['in_gauntlet_run_until']) <= time()) {
					echo $this->Html->link(__('View Results'), array('controller' => 'gauntlet_runs', 'action' => 'complete_run', $monster['Monster']['id']));
				}else{
					$now = new DateTime();
					$future_date = new DateTime($monster['Monster']['in_gauntlet_run_until']);
					
					$interval = $future_date->diff($now);
					echo 'Completes the Gauntlet in ';
					if($interval->h > 0) {
						echo $interval->h.' hour'.($interval->h == 1 ? '' : 's');
					}elseif($interval->i > 0) {
						echo $interval->i.' minute'.($interval->i == 1 ? '' : 's');
					}elseif($interval->s > 0) {
						echo $interval->s.' second'.($interval->s == 1 ? '' : 's');
					}
					echo '.';
				}
			}elseif(strtotime($monster['Monster']['resting_until']) > time()) {
				$now = new DateTime();
				$future_date = new DateTime($monster['Monster']['resting_until']);
				
				$interval = $future_date->diff($now);
				echo 'Resting from Battle for ';
				if($interval->h > 0) {
					echo $interval->h.' hour'.($interval->h == 1 ? '' : 's');
				}elseif($interval->i > 0) {
					echo $interval->i.' minute'.($interval->i == 1 ? '' : 's');
				}elseif($interval->s > 0) {
					echo $interval->s.' second'.($interval->s == 1 ? '' : 's');
				}
				echo '.';
			}else{
				if($battle_available) {
					echo $this->Html->link(__('Battle in the Gauntlet!'), array('controller' => 'gauntlet_runs', 'action' => 'start_run', $monster['Monster']['id']));
				}else{
					echo 'You can only have '.$user['User']['active_monster_limit'].' Monster'.($user['User']['active_monster_limit'] == 1 ? '' : 's').' active in the Gauntlet at a time.';
				}
			}
			?>
		</td>
		<td><?php echo $monster['Monster']['name']; ?>&nbsp;</td>
		<td><?php echo $monster['Type']['name']; if(!empty($monster['SecondaryType']['id'])) echo '/'.$monster['SecondaryType']['name']; ?>&nbsp;</td>
		<td>
			<?php 
				if(!empty($monster['Skill1']['id'])) {
					echo $this->Html->link($monster['Skill1']['name'], array('controller' => 'skills', 'action' => 'view', $monster['Skill1']['id']));
				}else{
					echo 'No Skill Set';
				}
			?>
		</td>
		<td>
			<?php 
				if(!empty($monster['Skill2']['id'])) {
					echo $this->Html->link($monster['Skill2']['name'], array('controller' => 'skills', 'action' => 'view', $monster['Skill2']['id']));
				}else{
					echo 'No Skill Set';
				}
			?>
		</td>
		<td>
			<?php 
				if(!empty($monster['Skill3']['id'])) {
					echo $this->Html->link($monster['Skill3']['name'], array('controller' => 'skills', 'action' => 'view', $monster['Skill3']['id']));
				}else{
					echo 'No Skill Set';
				}
			?>
		</td>
		<td>
			<?php 
				if(!empty($monster['Skill4']['id'])) {
					echo $this->Html->link($monster['Skill4']['name'], array('controller' => 'skills', 'action' => 'view', $monster['Skill4']['id']));
				}else{
					echo 'No Skill Set';
				}
			?>
		</td>
		<td>
			<?php 
				if(!empty($monster['Ultimate']['id'])) {
					echo $this->Html->link($monster['Ultimate']['name'], array('controller' => 'ultimates', 'action' => 'view', $monster['Ultimate']['id']));
				}else{
					echo 'No Ultimate Set';
				}
			?>
		</td>
		<td class="actions">
			<?php if($monster['Monster']['in_gauntlet_run']) {
				echo 'You cannot edit your monster<br>while it is in the Gauntlet.<br><br>';
			}elseif(strtotime($monster['Monster']['resting_until']) > time()) {
				echo 'You cannot edit your monster<br>while it is resting from Battle.<br><br>';
			}else{
				echo $this->Html->link(__('Edit Name'), array('controller' => 'monsters', 'action' => 'edit', $monster['Monster']['id']));
				echo $this->Html->link(__('Edit Skills'), array('controller' => 'monsters', 'action' => 'edit_skills', $monster['Monster']['id']));
				echo $this->Html->link(__('Edit Runes'), array('controller' => 'monsters', 'action' => 'edit_runes', $monster['Monster']['id']));
			}
			
				if($can_practice) {
					echo $this->Html->link(__('Practice'), array('controller' => 'battle', 'action' => 'practice', $monster['Monster']['id']));
				} ?>
		</td>
	</tr>
<?php endforeach; ?>
	</tbody>
	</table>
</div>
<?php echo $this->element('side_nav'); ?>
