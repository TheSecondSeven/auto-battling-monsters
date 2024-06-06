<?php $this->extend('../layout/dashboard'); ?>
<div class="quests form">
	<h2><?= $quest->title ?></h2>
	<div class="alert alert-label alert-success" style="text-align: left"><?= $quest->description ?></div>
	<table class="table">
		<thead>
			<tr>
				<th>Monsters</th>
				<th>Required Rest</th>
				<th>Persistent?</th>
				<th>Rewards</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?= $quest->get('monsters') ?></td>
				<td><?= $quest->get('required_rest_verbose') ?></td>
                <td><?= ($quest->persistent ? 'Yes' : 'No') ?></td>
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