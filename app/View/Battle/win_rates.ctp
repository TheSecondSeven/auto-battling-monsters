<div class="battle winRates">
	<h2><?php echo __('Win Rates'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<thead>
	<tr>
			<th>Name</th>
			<th>Ultimate</th>
			<th>Win %</th>
			<th>Loss %</th>
			<th>Tie %</th>
			<th>ELO Rating</th>
			<th>ELO Change</th>
	</tr>
	</thead>
	<tbody>
	<?php 
		$total_rating = 0;
		$total_change = 0;
	foreach ($total as $monster) {
		$total_matches = $monster['wins'] + $monster['losses'] + $monster['ties'];
		 ?>
	<tr>
		<td><?php echo $monster['name']; ?>&nbsp;</td>
		<td><?php echo $monster['ultimate']; ?>&nbsp;</td>
		<td><?php echo round($monster['wins'] / $total_matches * 100,2); ?>&nbsp;</td>
		<td><?php echo round($monster['losses'] / $total_matches * 100,2); ?>&nbsp;</td>
		<td><?php echo round($monster['ties'] / $total_matches * 100,2); ?>&nbsp;</td>
		<td><?php echo $monster['elo_rating']; $total_rating += $monster['elo_rating'];  ?>&nbsp;</td>
		<td><?php if($monster['elo_change'] >= 0) echo '+'; echo $monster['elo_change']; $total_change += $monster['elo_change']; ?>&nbsp;</td>
	</tr>
<?php } ?>
	
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>Average Rating: <?php echo $total_rating / count($total);  ?>&nbsp;</td>
		<td>Total Change: <?php if($total_change >= 0) echo '+'; echo $total_change;  ?>&nbsp;</td>
	</tr>
	</tbody>
	</table>
</div>