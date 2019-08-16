<div class="monsters form">
	<h2><?php echo __('Monster Leaderboard'); ?></h2>
	<table cellpadding="0" cellspacing="0">
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
			if($current_rating == null || $current_rating > $monster['Monster']['elo_rating']) {
				$current_rank = $next_rank;
				$current_rating = $monster['Monster']['elo_rating'];
			}
			$next_rank++;
			echo $current_rank;
			?>&nbsp;</td>
		<td><?php echo h($monster['Monster']['name']); ?>&nbsp;</td>
		<td><?php echo h($monster['User']['username']); ?>&nbsp;</td>
		<td style="text-align: center;"><?php echo round($monster['Monster']['elo_rating']); ?>&nbsp;</td>
		<td style="text-align: center;">
			<?php 
			echo $monster['Skill1']['name'];
			?>
		</td>
		<td style="text-align: center;">
			<?php 
			echo $monster['Skill2']['name'];
			?>
		</td>
		<td style="text-align: center;">
			<?php 
			echo $monster['Skill3']['name'];
			?>
		</td>
		<td style="text-align: center;">
			<?php 
			echo $monster['Skill4']['name'];
			?>
		</td>
		<td style="text-align: center;">
			<?php 
			echo $monster['Ultimate']['name'];
			?>
		</td>

	</tr>
	
<?php endforeach; ?>
	</tbody>
	</table>
</div>

<?php echo $this->element('side_nav'); ?>