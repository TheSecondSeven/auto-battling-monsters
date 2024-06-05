<script type="text/javascript">
	var originalRunData = '<?php echo $battlesJSON; ?>';
	var battleData = null;
	var battleIndex = 0;
	var timeRate = 35/50;
	var time = 0;
	var interval = null;

	var statusDescriptions = {<?php foreach($statuses as $index => $status) { if($index > 0) echo ','; echo $status->class.': "'.$status->description.'"'; } ?>};


	function previousBattle() {
		if(battleIndex > 0) {
			battleIndex = battleIndex - 1;
			restart();
		}
	}
	
	function nextBattle() {
		if(battleIndex < 9) {
			battleIndex = battleIndex + 1;
			restart();
		}
	}
	
	function setSpeed(speed) {
		$('.slow').removeClass('btn-primary');
		$('.medium').removeClass('btn-primary');
		$('.fast').removeClass('btn-primary');
		$('.slow').removeClass('btn-secondary');
		$('.medium').removeClass('btn-secondary');
		$('.fast').removeClass('btn-secondary');
		if(speed == 'slow') {
			timeRate = 20/50;
			$('.slow').addClass('btn-primary');
			$('.medium').addClass('btn-secondary');
			$('.fast').addClass('btn-secondary');
		}else if(speed == 'medium') {
			timeRate = 35/50;
			$('.slow').addClass('btn-secondary');
			$('.medium').addClass('btn-primary');
			$('.fast').addClass('btn-secondary');
		}else if(speed == 'fast') {
			timeRate = 50/50;
			$('.slow').addClass('btn-secondary');
			$('.medium').addClass('btn-secondary');
			$('.fast').addClass('btn-primary');
		}
		
	}
	
	function goToResults() {
		if(Object.keys(battleData.action_log).length > 0) {
			while(Object.keys(battleData.action_log).length > 1) {
				var nextTime = Object.keys(battleData.action_log)[0];
				delete battleData.action_log[nextTime];
			}
			$('.castCompletion').stop();
			$('.healthRemaining').stop();
			$('.messageLog').html('');
			time = 1000000;
		}
	}
	
	function restart() {
		$('#rewards').hide();
		clearInterval(interval);
		$('.castCompletion').stop();
		$('.healthRemaining').stop();
		$('.messageLog').html('');
		$('#battleInfo div').hide();
		$('#battleInfo #headline-info-'+battleIndex).show();
		var runData = JSON.parse(originalRunData);
		console.log(runData);
		battleData = Object.assign({}, runData[battleIndex]);
		setupStatusKey();
		time = 0;
		interval = window.setInterval(function(){
			updateScene(time, battleData);
			time += 50 * timeRate;
		}, 50);
	}
	
	function setupStatusKey() {
		$('.status-key ul').html('');
		var runData = JSON.parse(originalRunData);
		var data = runData[battleIndex];
		if (typeof data.used_buffs != "undefined") {
			for(statusKey in data.used_buffs) {
				$('.status-key ul').append('<li data-bs-toggle="tooltip" data-bs-placement="top" title="'+statusDescriptions[statusKey]+'" class="list-group-item"><div class="buff '+statusKey+'"></div><div>'+data.used_buffs[statusKey]+'</div></li>');
			}
		}
		if (typeof data.used_debuffs != "undefined") {
			for(statusKey in data.used_debuffs) {
				$('.status-key ul').append('<li data-bs-toggle="tooltip" data-bs-placement="top" title="'+statusDescriptions[statusKey]+'" class="list-group-item"><div class="debuff '+statusKey+'"></div><div>'+data.used_debuffs[statusKey]+'</div></li>');
			}
		}
		if (typeof data.used_statuses != "undefined") {
			for(statusKey in data.used_statuses) {
				$('.status-key ul').append('<li data-bs-toggle="tooltip" data-bs-placement="top" title="'+statusDescriptions[statusKey]+'" class="list-group-item"><div class="status '+statusKey+'"></div><div>'+data.used_statuses[statusKey]+'</div></li>');
			}
		}
		var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
		var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
			return new bootstrap.Tooltip(tooltipTriggerEl)
		});
	}
	
	function updateScene(currentTime, battleData) {
		if(Object.keys(battleData.action_log).length > 0) {
			var nextTime = Object.keys(battleData.action_log)[0];
			if(time >= parseInt(nextTime)) {
				$('.monsterDiv').each(function() {
					$(this).data('updated',0);
				});
				for(monsterID in battleData.action_log[nextTime].state) {
					updateMonster(battleData.action_log[nextTime],monsterID);
				}
				$('.monsterDiv').each(function() {
					if($(this).data('updated') == 0) {
						$(this).remove();
					}
				});
				delete battleData.action_log[nextTime];
			}
		}else{
			$('.castCompletion').stop();
			$('.healthRemaining').finish();
			$('#rewards').show();
			clearInterval(interval);
		}
	}
	
	function updateMonster(data, monsterID) {
		var state = data.state[monsterID];
		if(!($($('#'+monsterID)).length > 0)) {
			var teamID = state.team;
			$('#team-'+teamID).append('<div id="'+monsterID+'" class="monsterDiv"><div class="portrait"><div class="statuses"></div><div class="combatText"></div><div class="info"><div class="name"></div></div></div><div class="clear-fix"></div><div class="healthBar"><div class="healthRemaining"></div><div class="healthText"></div></div><div class="clear-fix"></div><div class="castBar"><div class="castCompletion"></div><div class="castText"></div></div><div class="clear-fix"></div><ul class="messageLog"></ul></div>');		
		}
		var monsterDiv = $('#'+monsterID);
		$(monsterDiv).data('updated', 1);
		var state = data.state[monsterID];
		
		$('.name',monsterDiv).html(state.name.toString());
		var healthRemaining = Math.max(0,Math.floor(state.current_health / state.max_health * 100));
		$('.healthText',monsterDiv).html(Math.max(0,state.current_health).toString()+'/'+state.max_health.toString());
		$('.healthRemaining',monsterDiv).animate({
			width : healthRemaining.toString()+"%",
			backgroundColor : getColorForPercentage(healthRemaining / 100)
		},300);
		
		var statuses = '';
		for(status in state.statuses) {
			if(typeof state.statuses[status].stacks != null && parseInt(state.statuses[status].stacks) > 1) {
				statuses += '<div class="status '+status+'">'+state.statuses[status].stacks+'</div>';
			}else{
				statuses += '<div class="status '+status+'"></div>';
			}
		}
		for(status in state.buffs) {
			if(typeof state.buffs[status].stacks != null && parseInt(state.buffs[status].stacks) > 1) {
				statuses += '<div class="buff '+status+'">'+state.buffs[status].stacks+'</div>';
			}else{
				statuses += '<div class="buff '+status+'"></div>';
			}
		}
		for(status in state.debuffs) {
			if(typeof state.debuffs[status].stacks != null && parseInt(state.debuffs[status].stacks) > 1) {
				statuses += '<div class="debuff '+status+'">'+state.debuffs[status].stacks+'</div>';
			}else{
				statuses += '<div class="debuff '+status+'"></div>';
			}
		}
		$('.statuses', monsterDiv).html(statuses);
		
		if (typeof data.messages != "undefined") {
			if (typeof data.messages[monsterID] != "undefined") {
				var monsterActionLog = data.messages[monsterID];
				
				for(var i = 0; i < monsterActionLog.length; i++) {
					var hasMessage = false;
					var logHTML = '<li class="group"><ul>';
					for(var j = 0; j < monsterActionLog[i].length; j++) {
						var type = monsterActionLog[i][j].type;
						<?php if(!empty($_GET['debug'])) { ?>
						hasMessage = true;
						logHTML += '<li>'+monsterActionLog[i][j].text+'</li>';
						<?php }else{ ?>
						if(!['debuff_lost','buff_lost','begin_cast','skill_use','healing_over_time','damage_over_time','burn_damage'].includes(type)) {
							hasMessage = true;
							logHTML += '<li>'+monsterActionLog[i][j].text+'</li>';
						}
						<?php } ?>
					}
					logHTML += '</ul></li>';
					if(hasMessage) {
						if(Object.keys(battleData.action_log).length == 1) {
							$('.messageLog',monsterDiv).prepend(logHTML).children(':first').hide().slideDown('fast');
						}else{
							$('.messageLog',monsterDiv).prepend(logHTML).children(':first').hide().slideDown('fast').delay(4000).fadeOut(300);
						}
					}
				}
			}
		}
		if (typeof data.casting != "undefined") {
			if (typeof data.casting[monsterID] != "undefined") {
				var castStatus = data.casting[monsterID];
				if(castStatus.status == 'start') {
					$('.castText',monsterDiv).html(castStatus.name);
					$('.castCompletion',monsterDiv).removeClass('completed');
					$('.castCompletion',monsterDiv).removeClass('interrupted');
					$('.castCompletion',monsterDiv).width(0);
					$('.castCompletion',monsterDiv).animate({
						width : "100%"
					},Math.round(parseFloat(castStatus.cast_time) / timeRate));
				}else if(castStatus.status == 'completed') {
					$('.castCompletion',monsterDiv).addClass('completed');
				}else if(castStatus.status == 'interrupted') {
					$('.castCompletion',monsterDiv).addClass('interrupted');
					$('.castCompletion',monsterDiv).stop();
				}else if(castStatus.status == 'instant') {
					$('.castCompletion',monsterDiv).addClass('completed');
					$('.castCompletion',monsterDiv).removeClass('interrupted');
					$('.castCompletion',monsterDiv).width('100%');
					$('.castText',monsterDiv).html(castStatus.name);
				}
			}
		}
		if (typeof data.healthChange != "undefined") {
			if (typeof data.healthChange[monsterID] != "undefined") {
				var monsterHealthChanges = data.healthChange[monsterID];
				
				for(var i = 0; i < monsterHealthChanges.length; i++) {
					var diameter = 150;
					if(parseInt(monsterHealthChanges[i].amount) > 0) {
						$('.combatText',monsterDiv)
							.prepend('<div class="combatTextItem heal '+monsterHealthChanges[i].type+'">+'+monsterHealthChanges[i].amount+'</div>')
							.children(':first')
							.hide()
							.fadeIn(100)
							.addClass('grow')
							.animate({
								left: Math.floor(Math.random() * diameter - diameter / 2),
								top: Math.floor(Math.random() * diameter - diameter / 2)
							}, 1, function() {
								$(this)
								.fadeOut(300);
							});
					}else if(parseInt(monsterHealthChanges[i].amount) < 0){
						$('.combatText',monsterDiv)
						.prepend('<div class="combatTextItem damage '+monsterHealthChanges[i].type+'">'+monsterHealthChanges[i].amount+'</div>')
						.children(':first')
						.hide()
						.fadeIn(100, function() {
							$(this)
							.addClass('grow');
							$(this)
							.animate({
								left: Math.floor(Math.random() * diameter - diameter / 2),
								top: Math.floor(Math.random() * diameter - diameter / 2)
							}, {
								duration: 400,
								easing: 'linear',
								complete: function() {
									$(this)
									.fadeOut(100, function() {
										$(this).remove();
									});
								}
							});
						});
					}
				}
			}
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

$( document ).ready(function() {
	restart();
});
</script>