<?php $this->extend('../layout/TwitterBootstrap/dashboard'); ?>
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
	<h2><?php echo __($gauntlet_run->monster->name.'\''.(substr($gauntlet_run->monster->name, -1) == 's' ? '' : 's').' Results: '.$gauntlet_run->wins.' Win'.($gauntlet_run->wins == 1 ? '' : 's').'!'); ?></h2>
	<table class="table table-striped" width="500px">
	<thead>
	<tr>
		<th>Opponent Monster</th>
		<th>User</th>
		<th>Result</th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($gauntlet_run->gauntlet_run_battles as $battle): ?>
	<tr>
		<td><?php echo $battle->opponent->name; ?>&nbsp;</td>
		<td><?php echo $battle->opponent->user->username; ?>&nbsp;</td>
		<td><?php echo $battle->result; ?>&nbsp;</td>
	</tr>
<?php endforeach; ?>
	</tbody>
</table>
<a href="/view-gauntlet-battles/<?php echo $gauntlet_run->id; ?>">Watch the Battles</a>
</div>
</div>

<div style="clear: both; display: block; height: 50px;">
</div>
<div class="rewards">
<div style="width: 500px; display: inline-block;">
	<h2><?php echo __('Rewards'); ?></h2>
	<h3>Winning at least 5 guarantees a Rare, 7 an Epic, and all 10 guarantees you a Legendary!</h3>
	<table class="table table-striped" width="500px">
	<thead>
	<tr>
		<th style="text-align: center;">Reward</th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($gauntlet_run->gauntlet_run_rewards as $reward): ?>
	<tr class="<?php echo strtolower($reward->rarity); ?>">
		<td style="text-align: center;">
			<?php if($reward->type == 'Skill') {
				echo 'Skill Unlock: '.$reward->skill->name.'!';
			}elseif($reward->type == 'Ultimate') {
				echo 'Ultimate Unlock: '.$reward->ultimate->name;
			}elseif($reward->type == 'Gems') {
				echo 'You earned '.$reward->amount.' Gem'.($reward->amount == 1 ? '' : 's').'!';
			}elseif($reward->type == 'Gold') {
				echo 'You earned '.$reward->amount.' Gold!';
			}elseif($reward->type == 'Rune Shards') {
				echo 'You earned '.$reward->amount.' Rune Shards!';
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