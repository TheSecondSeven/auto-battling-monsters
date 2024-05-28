<?php $this->extend('../layout/TwitterBootstrap/dashboard'); ?>
<div class="monsters form">
	<h2><?php echo __('Monster Leaderboard'); ?></h2>
	<table class="table table-striped">
	<thead>
	<tr>
			<th style="text-align: center;">Rank</th>
			<th>Name</th>
			<th>User</th>
			<th style="text-align: center;">Rating</th>
			<th style="text-align: center;">1st Skill</th>
			<th style="text-align: center;">2nd Skill</th>
			<th style="text-align: center;">3rd Skill</th>
			<th style="text-align: center;">4th Skill</th>
			<th style="text-align: center;">Ultimate</th>
	</tr>
	</thead>
	<tbody>
	<?php
	$current_rating = null;
	$current_rank = 0;
	$next_rank = 1;
	foreach ($monsters as $monster): ?>
	<tr>
		<td style="text-align: center;"><?php 
			if($current_rating == null || $current_rating > $monster->elo_rating) {
				$current_rank = $next_rank;
				$current_rating = $monster->elo_rating;
			}
			$next_rank++;
			echo $current_rank;
			?>&nbsp;</td>
		<td><?php echo h($monster->name); ?>&nbsp;</td>
		<td><?php echo h($monster->user->username); ?>&nbsp;</td>
		<td style="text-align: center;"><?php echo round($monster->elo_rating); ?>&nbsp;</td>
		<td style="text-align: center;">
			<?php 
			echo $monster->skill1->name;
			?>
		</td>
		<td style="text-align: center;">
			<?php 
			echo $monster->skill2->name;
			?>
		</td>
		<td style="text-align: center;">
			<?php 
			echo $monster->skill3->name;
			?>
		</td>
		<td style="text-align: center;">
			<?php 
			echo $monster->skill4->name;
			?>
		</td>
		<td style="text-align: center;">
			<?php 
			echo $monster->ultimate->name;
			?>
		</td>

	</tr>
	
<?php endforeach; ?>
	</tbody>
	</table>
</div>