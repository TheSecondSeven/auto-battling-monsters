<?php $this->extend('../layout/TwitterBootstrap/dashboard'); ?>
<style>
	.content {
		height: 750px;
		text-align: center;
	}
	#teams {
		text-align: center;
	}
	#teams-container {
		display: inline-block;
	}
	#battleInfo {
		display: inline-block;
		float: none !important;
		width: 100% !important;
	}
	#battleInfo div {
		display: none;
	}
	.headline {
		font-size: 24px;
	}
	.result {
		font-size: 30px;
		font-weight: bold;
	}
	#speed-controls {
		
		display: inline-block;
		float: none !important;
		width: 100% !important;
	}
	
	#team-1 {
		width: 200px;
		float: left;
	}
	#team-2 {
		width: 200px;
		float: left;
	}
	.team {
		margin: 20px;
	}
	.monsterDiv {
		width: 100%;
		min-height: 300px;
	}
	
	.clear-fix {
		clear: both;
	}
	
	.combatText {
		clear: both;
		height: 20px;
	}
	.combatTextItem {
		float: left;
		margin: 0 0 0 5px;
		width: 20px;
		height: 20px;
		font-size: 12px;
		font-weight: bold;
		color: white;
		line-height: 30px;
		text-align: center;
	}
	.combatText .heal {
		color: #3cb44b;
	}
	.combatText .damage {
		color: #e6194b;
	}
	.info {
		margin-top: 10px;
		height: 20px;
	}
	.name {
		float: left;
		height: 20px;
		line-height: 20px;
	}
	.statuses {
		float: left;
	}
	.statuses div {
		float: left;
		margin: 0 0 0 5px;
		width: 20px;
		height: 20px;	
		word-wrap: break-word;
		font-size: 14px;
		font-weight: bold;
		color: white;
		line-height: 20px;
		text-align: center;
	}
	.status-key {
		/*float: right;*/
	}
	.status-key ul {
		text-align: center;
	}
	.status-key li {
		text-align: center;
	}
	.status-key .status, .status-key .buff, .status-key .debuff {
		margin: 0;
		width: 20px;
		height: 20px;	
		word-wrap: break-word;
		font-size: 14px;
		font-weight: bold;
		color: white;
		line-height: 20px;
		text-align: center;
		display: inline-block;
	}
	
	.status {
		border:2px solid #454545;
	}
	
	/*buffs*/
	.buff {
		border:2px solid #3cb44b;	
	}
	
	/*debuffs*/
	.debuff {
		border:2px solid #e6194b;
	}
	<?php foreach($statuses as $status) { 
		echo '
	.'.$status->class.' {
		color: '.$status->text_color.' !important;
		background-color: #'.$status->hex.';
	}';
		
	} ?>
	
	
	.healthBar {
		margin-top: 10px;
		clear: both;
		border:1px solid black;
		height: 25px;
		width: 200px;
	}
	.healthRemaining {
		background-color: #00FF00;
		height: 100%;
		float: left;
	}
	.healthText {
		height: 25px;
		line-height: 25px;
		position: absolute;
		width: 200px;
		text-align: center;
	}
	.castBar {
		clear: both;
		border:1px solid black;
		height: 25px;
		width: 200px;
	}
	.castCompletion {
		position: relative;
		background-color: #49abf7;
		height: 100%;
		float: left;
	}
	.castCompletion.interrupted {
		background-color: #e85225;
	}
	.castCompletion.completed {
		background-color: #9bf047;
	}
	.castText {
		height: 25px;
		line-height: 25px;
		position: absolute;
		width: 200px;
		text-align: center;
	}
	.messageLog {
		margin: 0;
		list-style-type: none;
		width: 202px;
	}
	.messageLog li.group {
		margin: 0;
		padding: 0 5px 0 5px;
		border:1px solid black;
	}
	
	.messageLog li ul {
		margin: 0;
		list-style-type: none;
	}
	.messageLog li ul li {
		margin: 0;
	}
</style>
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
	<div class="status-key">
		<ul class="list-group list-group-horizontal">
  
		</ul>
	</div>
	<br>
	<div id="speed-controls" class="actions">
		<a class="slow btn btn-secondary" onclick="setSpeed('slow'); return false;" type="button">Slow</a>
		<a class="medium btn btn-primary"  onclick="setSpeed('medium'); return false;" type="button">Medium</a>
		<a class="fast btn btn-secondary" onclick="setSpeed('fast'); return false;" type="button">Fast</a>
		<br>
		<br>
		<a class="btn btn-primary" onclick="previousBattle(); return false;" type="button">Previous Battle</a>
		<a class="btn btn-primary" onclick="goToResults(); return false;" type="button">Battle Results</a>
		<a class="btn btn-primary" onclick="restart(); return false;" type="button">Restart Battle</a>
		<a class="btn btn-primary" onclick="nextBattle(); return false;" type="button">Next Battle</a>
		<br>
		<br>
		<a href="/view-gauntlet-results/<?php echo $gauntlet_run->id; ?>" type="button">Gauntlet Results</a>
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