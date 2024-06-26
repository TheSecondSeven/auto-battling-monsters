<?php $this->extend('../layout/dashboard'); ?>
<?php echo $this->element('battle_css'); ?>
<div class="content form">
	
	<div class="status-key">
		<ul class="list-group list-group-horizontal">
		</ul>
	</div>
	<br>
	<br>
	<div id="speed-controls" class="actions list-group list-group-horizontal">
		<div class="list-group-item">
			<a class="slow btn btn-secondary" onclick="setSpeed('slow'); return false;" type="button">Slow</a>
			<a class="medium btn btn-primary"  onclick="setSpeed('medium'); return false;" type="button">Medium</a>
			<a class="fast btn btn-secondary" onclick="setSpeed('fast'); return false;" type="button">Fast</a>
		</div>
		<div class="list-group-item">
			<a class="btn btn-success" onclick="restart(); return false;" type="button">Restart</a>
		</div>	
	</div>
	<?php if(!empty($quest->user_quest_rewards)) { ?>
		<br>
		<br>
	<div id="rewards" style="display:none;width:300px;margin:0 auto;">
		<h3>You Won!</h3>
		For a reward you have received:
		<div class="list-group" style="width: 300px; margin: 0 auto;">
		<?php foreach($quest->user_quest_rewards as $reward) { ?>
			<span class="list-group-item"><?= $reward->get('reward') ?></span>
		<?php } ?>
		</div>
		<br>
		<div>
			<?= $this->Html->link('Back to Campaign', ['controller' => 'quests', 'action' => 'index'], ['class' => 'btn btn-success']); ?>
			<?php if(!empty($quest->child_quests) && count($quest->child_quests) == 1) { ?>
				<?= $this->Html->link('Next Quest', ['controller' => 'quests', 'action' => 'view',$quest->child_quests[0]->id], ['class' => 'btn btn-primary']); ?>
			<?php } ?>
		</div>
	</div>
	<?php }else{ ?>
		<br>
		<br>
	<div id="rewards" style="display:none;width:300px;margin:0 auto;">
		<h3>You Lost</h3>
		<br>
		<?= $this->Html->link('Try Again', ['controller' => 'quests', 'action' => 'view', $quest->id], ['class' => 'btn btn-success']); ?>
	</div>
	<?php } ?>
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