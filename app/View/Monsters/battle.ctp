<style>
	.healthBar {
		border:1px solid black;
		height: 25px;
		width: 200px;
	}
	.healthRemaining {
		background-color: #00FF00;
		height: 100%;
		float: left;
	}
</style>


<div id="first_monster">
	<div>
		<?php echo $first_monster['Monster']['name']; ?> | <span id="firstMonsterCurrentHP">HP: <?php echo $first_monster['Stats']['health']; ?></span><span id="firstMonsterStatuses"></span>
	</div>
	<div class="healthBar" style="">
		<div id="firstMonsterHealthRemaining" class="healthRemaining" style="width: 100%;">
		</div>
	</div>
</div>

<div id="first_monster">
	<div>
		<?php echo $second_monster['Monster']['name']; ?> | <span id="secondMonsterCurrentHP">HP: <?php echo $second_monster['Stats']['health']; ?></span><span id="secondMonsterStatuses"></span>
	</div>
	<div class="healthBar" style="">
		<div id="secondMonsterHealthRemaining" class="healthRemaining" style="width: 100%;">
		</div>
	</div>
</div>

<ul id="messageLog">
	
</ul>


<script type="text/javascript">
	var battleData = JSON.parse('<?php echo $battleJSON; ?>');
	
	var time = 0;
	var interval = window.setInterval(function(){
		updateScene(time);
		time += 25;
	}, 50);
	
	function updateScene(currentTime) {
		if(Object.keys(battleData).length > 0) {
			var nextTime = Object.keys(battleData)[0];
			if(time >= parseInt(nextTime)) {
				var actionLog = battleData[nextTime];
				var firstMonsterHealthRemaining = Math.max(0,Math.floor(actionLog.state.first_monster.current_health / actionLog.state.first_monster.max_health * 100));
				$('#firstMonsterCurrentHP').html('HP: '+Math.max(0,actionLog.state.first_monster.current_health).toString());
				$('#firstMonsterHealthRemaining').animate({
					width : firstMonsterHealthRemaining.toString()+"%",
					backgroundColor : getColorForPercentage(firstMonsterHealthRemaining / 100)
				},300);
				
				var secondMonsterHealthRemaining = Math.max(0,Math.floor(actionLog.state.second_monster.current_health / actionLog.state.second_monster.max_health * 100));
				$('#secondMonsterCurrentHP').html('HP: '+Math.max(0,actionLog.state.second_monster.current_health).toString());
				$('#secondMonsterHealthRemaining').animate({
					width : secondMonsterHealthRemaining.toString()+"%",
					backgroundColor : getColorForPercentage(secondMonsterHealthRemaining / 100)
				},300);
				
				var messageLog = document.getElementById("messageLog");
				messageLog.innerHTML = "";
				for(var i = 0; i < actionLog.messages.length; i++) {
					for(var j = 0; j < actionLog.messages[i].length; j++) {
						var li = document.createElement("li");
						li.appendChild(document.createTextNode(actionLog.messages[i][j]));
						messageLog.appendChild(li);
					}
				}
				
				delete battleData[nextTime];
			}
		}else{
			clearInterval(interval);
		}
	}
	
var percentColors = [
    { pct: 0.0, color: { r: 0xff, g: 0x00, b: 0 } },
    { pct: 0.5, color: { r: 0xff, g: 0xff, b: 0 } },
    { pct: 1.0, color: { r: 0x00, g: 0xff, b: 0 } }
];

var getColorForPercentage = function(pct) {
    for (var i = 1; i < percentColors.length - 1; i++) {
        if (pct < percentColors[i].pct) {
            break;
        }
    }
    var lower = percentColors[i - 1];
    var upper = percentColors[i];
    var range = upper.pct - lower.pct;
    var rangePct = (pct - lower.pct) / range;
    var pctLower = 1 - rangePct;
    var pctUpper = rangePct;
    var color = {
        r: Math.floor(lower.color.r * pctLower + upper.color.r * pctUpper),
        g: Math.floor(lower.color.g * pctLower + upper.color.g * pctUpper),
        b: Math.floor(lower.color.b * pctLower + upper.color.b * pctUpper)
    };
    return 'rgb(' + [color.r, color.g, color.b].join(',') + ')';
    // or output as hex if preferred
} 
	
	
	
</script>