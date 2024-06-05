<style>
	.content {
		height: 1550px;
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
		margin: 0 auto;
  align-items: center;
  justify-content: center;
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

	.portrait {
		width: 200px;
		height: 200px;
		position: relative;
		border: 2px solid black;
		background-color: #75dbdb61;
	}

	.portrait.fire {
		background-color: #e6194b;
	}
	
	.clear-fix {
		clear: both;
	}
	
	.combatText {
		height: 20px;
		width: 20px;
		line-height: 20px;
		position:absolute;
		top: calc(50% - 10px);
		left: calc(50% - 10px);
	}
	.combatTextItem {
  		transform: scale(1);
		float: left;
		margin: 0 0 0 5px;
		width: 20px;
		height: 20px;
		font-size: 12px;
		font-weight: bold;
		color: white;
		line-height: 20px;
		text-align: center;
  		transition: transform 0.5s;
		position: absolute;
	}
	.combatTextItem.grow {
  		transform: scale(3);
	}
	.combatText .heal {
		color: #3cb44b;
	}
	.combatText .damage {
		color: #e6194b;
	}
	.info {
		width: 100%;
		line-height: 20px;

		position: absolute;
		bottom: 3px;
		left: 0px;
	}
	.name {
		margin: 0 auto;
		height: 20px;
		line-height: 20px;
	}
	.statuses {
		line-height: 30px;
		position: absolute;
		top: 0px;
		left: 0px;
	}
	.statuses div {
		float: left;
		margin: 7.666px 0 0 7.666px;
		width: 30px;
		height: 30px;	
		word-wrap: break-word;
		font-size: 14px;
		font-weight: bold;
		color: white;
		line-height: 26px;
		text-align: center;
	}
	.status-key {
		display: inline-block;
	}
	.status-key ul {
		text-align: center;
	}
	.status-key li {
		text-align: center;
	}
	.status-key .status, .status-key .buff, .status-key .debuff {
		margin: 0;
		width: 30px;
		height: 30px;	
		word-wrap: break-word;
		font-size: 14px;
		font-weight: bold;
		color: white;
		line-height: 26px;
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
		background-color: '.$status->hex.';
	}';
		
	} ?>
	
	
	.healthBar {
		margin-top: 1px;
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
		margin-top: 1px;
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
		margin: 1px 0 0 0;
		list-style-type: none;
		width: 200px;
		padding-left: 0px;
	}
	.messageLog li.group {
		margin: 1px 0 0 0;
		padding: 0 5px 0 5px;
		border:1px solid black;
	}
	
	.messageLog li ul {
    padding-left: 15px;
    margin: 0;
    list-style-type: disclosure-closed;
	}
	.messageLog li ul li {
		margin: 0;
		text-align: left;
	}
</style>