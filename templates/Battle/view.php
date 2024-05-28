<?php $this->extend('../layout/dashboard'); ?>
<?php echo $this->element('battle_css'); ?>
<div class="content form">
	
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
		<a class="btn btn-primary" onclick="restart(); return false;" type="button">Restart</a>
		<a class="btn btn-primary" onclick="goToResults(); return false;" type="button">Results</a>
	</div>
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