<?php $this->extend('../layout/dashboard'); ?>
<?php echo $this->element('battle_css'); ?>
<div class="content form">
	<div id="battleInfo">
		<?php foreach($gauntlet_run->gauntlet_run_battles as $index=>$battle) {
			echo '<div id="headline-info-'.$index.'">';
			echo '<span class="headline">'.$gauntlet_run->monster->name.' VS '.$battle->opponent->name.'</span>';
			echo '<br>';
			echo '<span class="result">'.$battle->result.'</span>';
			echo '</div>';
		} ?>
	</div>
		<br>
		<a class="btn btn-success" href="/view-gauntlet-results/<?php echo $gauntlet_run->id; ?>" type="button">View Run Results and Rewards</a>
	<br>
	<br>
	<div class="status-key">
		<ul class="list-group list-group-horizontal">
  
		</ul>
	</div>
	<br>
	<br>
	<div id="speed-controls" class="actions">
		<a class="slow btn btn-secondary" onclick="setSpeed('slow'); return false;" type="button">Slow</a>
		<a class="medium btn btn-primary"  onclick="setSpeed('medium'); return false;" type="button">Medium</a>
		<a class="fast btn btn-secondary" onclick="setSpeed('fast'); return false;" type="button">Fast</a>
		<br>
		<br>
		<a class="btn btn-primary" onclick="restart(); return false;" type="button">Restart Battle</a>
		<a class="btn btn-primary" onclick="goToResults(); return false;" type="button">Battle Results</a>
		<br>
		<br>
		<a class="btn btn-primary" onclick="previousBattle(); return false;" type="button">Previous Battle</a>
		<a class="btn btn-primary" onclick="nextBattle(); return false;" type="button">Next Battle</a>
	</div>
	<br>
	<div id="teams">
		<div id="teams-container">
			<div id="team-1" class="team">
			</div>
			<div id="team-2" class="team">
			</div>
		</div>
	</div>
</div>
<?php echo $this->element('2_team_battle_javascript'); ?>