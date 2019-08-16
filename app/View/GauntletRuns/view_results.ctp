<style>
	.reward {
		width: 200px;
		height: 200px;
		margin: 10px;
		display: inline-block;
		border-radius: 20px;
		border: 2px solid black;
		padding: 20px;
	}
	.rarity-icon {
		width: 20px;
		height: 20px;
		margin: 10px;
	}
	.uncommon {
		-webkit-box-shadow:inset 0 0 20px #4dd64d; 
	    -moz-box-shadow:inset  0 0 20px #4dd64d; 
	    box-shadow:inset 0 0 20px #4dd64d;
	}
	.rare {
		-webkit-box-shadow:inset 0 0 20px #1676e4; 
	    -moz-box-shadow:inset  0 0 20px #1676e4; 
	    box-shadow:inset 0 0 20px #1676e4;
	}
	.epic {
		-webkit-box-shadow:inset 0 0 20px #8231c8; 
	    -moz-box-shadow:inset  0 0 20px #8231c8; 
	    box-shadow:inset 0 0 20px #8231c8;
	}
	.legendary {
		-webkit-box-shadow:inset 0 0 20px #ffd900; 
	    -moz-box-shadow:inset  0 0 20px #ffd900; 
	    box-shadow:inset 0 0 20px #ffd900;
	}
	.vertical-align-middle {
		display: table-cell;
		vertical-align: middle;
		height: 200px;
	}
</style>
<div class="form">
<div class="rewards">
<div style="width: 500px; display: inline-block;">
	<h2><?php echo __($gauntlet_run['Monster']['name'].'\''.(substr($gauntlet_run['Monster']['name'], -1) == 's' ? '' : 's').' Results: '.$gauntlet_run['GauntletRun']['wins'].' Win'.($gauntlet_run['GauntletRun']['wins'] == 1 ? '' : 's').'!'); ?></h2>
	<table cellpadding="0" cellspacing="0" width="500px">
	<thead>
	<tr>
		<th>Opponent Monster</th>
		<th>User</th>
		<th>Result</th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($gauntlet_run['GauntletRunBattle'] as $battle): ?>
	<tr>
		<td><?php echo $battle['Opponent']['name']; ?>&nbsp;</td>
		<td><?php echo $battle['Opponent']['User']['username']; ?>&nbsp;</td>
		<td><?php echo $battle['result']; ?>&nbsp;</td>
	</tr>
<?php endforeach; ?>
	</tbody>
</table>
<a href="/view-gauntlet-battles/<?php echo $gauntlet_run['GauntletRun']['id']; ?>">Watch the Battles</a>
</div>
</div>

<div style="clear: both; display: block; height: 50px;">
</div>
<div class="rewards">
<div style="width: 500px; display: inline-block;">
	<h2><?php echo __('Rewards'); ?></h2>
	<h3>Winning at least 5 guarantees a Rare, 7 an Epic, and all 10 guarantees you a Legendary!</h3>
	<table cellpadding="0" cellspacing="0" width="500px">
	<thead>
	<tr>
		<th style="text-align: center;">Reward</th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($gauntlet_run['GauntletRunReward'] as $reward): ?>
	<tr class="<?php echo strtolower($reward['rarity']); ?>">
		<td style="text-align: center;">
			<?php if($reward['type'] == 'Skill') {
				echo 'Skill Unlock: '.$reward['Skill']['name'].'!';
			}elseif($reward['type'] == 'Ultimate') {
				echo 'Ultimate Unlock: '.$reward['Ultimate']['name'];
			}elseif($reward['type'] == 'Gems') {
				echo 'You earned '.$reward['amount'].' Gem'.($reward['amount'] == 1 ? '' : 's').'!';
			}elseif($reward['type'] == 'Gold') {
				echo 'You earned '.$reward['amount'].' Gold!';
			}elseif($reward['type'] == 'Rune Shards') {
				echo 'You earned '.$reward['amount'].' Rune Shards!';
			}
			?>
		</td>
	</tr>
<?php endforeach; ?>
	</tbody>
</table>
</div>
</div>
</div>
<?php /*
<div class="rewards">
<?php
if($gauntlet_run['GauntletRun']['number_of_rewards_chosen'] > 0) {
	echo '<h2>'.$gauntlet_run['GauntletRun']['number_of_reward_picks'].' Rewards : '.($gauntlet_run['GauntletRun']['number_of_reward_picks'] - $gauntlet_run['GauntletRun']['number_of_rewards_chosen']).' Pick'.($gauntlet_run['GauntletRun']['number_of_reward_picks'] - $gauntlet_run['GauntletRun']['number_of_rewards_chosen'] == 1 ? '' : 's').' Remain'.($gauntlet_run['GauntletRun']['number_of_reward_picks'] - $gauntlet_run['GauntletRun']['number_of_rewards_chosen'] == 1 ? 's' : '').'</h2>';
}else{
	echo '<h2>Pick '.$gauntlet_run['GauntletRun']['number_of_reward_picks'].' Rewards';
}
echo '</h2>';
echo '</div>';
echo '<div class="rewards">';	
foreach($gauntlet_run['AvailableReward'] as $available_reward) {
	echo '<a href="/choose-reward/'.$available_reward['id'].'"><div class="reward '.strtolower($available_reward['rarity']).'"><div class="vertical-align-middle"><div>';
	if($available_reward['type'] == 'Skill') {
		echo $available_reward['Skill']['rarity'].' '.$available_reward['Skill']['Type']['name'].' Skill<br>'.$available_reward['Skill']['name'].'<br><br>'.$available_reward['Skill']['description'];
	}elseif($available_reward['type'] == 'Ultimate') {
		echo $available_reward['Ultimate']['rarity'].' '.$available_reward['Ultimate']['Type']['name'].' Ultimate<br>'.$available_reward['Ultimate']['name'].'<br><br>'.$available_reward['Ultimate']['description'];
	}elseif($available_reward['type'] == 'Augment') {
		echo $available_reward['Augment']['rarity'].' Rune<br>'.$available_reward['Augment']['name'].'<br><br>'.$available_reward['Augment']['description'];
		echo '<br><br>';
		$slot_text = '';
		$slot_count = 0;
		if($available_reward['Augment']['skill_1']) {
			if($slot_text != '')
				$slot_text .= ', ';
			$slot_text .= '1st Skill';
			$slot_count++;
		}
		if($available_reward['Augment']['skill_2']) {
			if($slot_text != '')
				$slot_text .= ', ';
			$slot_text .= '2nd Skill';
			$slot_count++;
		}
		if($available_reward['Augment']['skill_3']) {
			if($slot_text != '')
				$slot_text .= ', ';
			$slot_text .= '3rd Skill';
			$slot_count++;
		}
		if($available_reward['Augment']['skill_4']) {
			if($slot_text != '')
				$slot_text .= ', ';
			$slot_text .= '4th Skill';
			$slot_count++;
		}
		if($available_reward['Augment']['ultimate']) {
			if($slot_text != '')
				$slot_text .= ', ';
			$slot_text .= 'Ultimate';
			$slot_count++;
		}
		echo 'Slot'.($slot_count == 1 ? '' : 's').': '.$slot_text;
	}elseif($available_reward['type'] == 'Dual Type Monster') {
		echo $available_reward['Type']['name'].' and '.$available_reward['SecondaryType']['name'].'<br>Dual Type Monster!';
	}elseif($available_reward['type'] == 'Monster') {
		echo $available_reward['Type']['name'].' Type Monster!';
	}elseif($available_reward['type'] == 'Gems') {
		echo $available_reward['amount'].' Gem'.($available_reward['amount'] == 1 ? '' : 's').'!';
	}elseif($available_reward['type'] == 'Gold') {
		echo $available_reward['amount'].' Gold!';
	}
	echo '</div></div></div></a>';
}
echo '</div>';

?>
</div>
<?php */ ?>
<?php echo $this->element('side_nav'); ?>