<?php $this->extend('../layout/dashboard'); ?>
<div class="quests form">
<h2><?php echo __('Quest'); ?></h2>
	<table class="table">
		<thead>
			<tr>
				<th>Quest</th>
				<th>Required Rest</th>
				<th>Monsters</th>
				<th>Rewards</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?= $quest->title ?><br><?= $quest->description ?></td>
				<td><?= (!empty($quest->required_rest) ? $quest->required_rest : 'None') ?></td>
				<td><?= $quest->get('monsters') ?></td>
				<td><?= $quest->get('rewards') ?></td>
			</tr>
		</tbody>
	</table>
	<?php if(empty($quest->quest_monsters) || empty($quest->quest_rewards)) { ?>
		<p>This quest isn't ready yet. Please check back later!</p>
	
	<?php }elseif(count($available_monsters)) { ?>
    <div class="mb-3">
		<?= $this->Form->create() ?>
        <?= $this->Form->control('monster_id', ['label' => 'Choose Your Monster','options' => $available_monsters]); ?>
        <?= $this->Form->submit(__('Venture Forth!')); ?>
        <?= $this->Form->end() ?>
	</div>
	<?php }else{ ?>
		<p>You don't have any monsters ready to quest. To be able to quest, a monster needs to have its move set completed, be fully rested, and cannot be running the Gauntlet.
	<?php } ?>
</div>