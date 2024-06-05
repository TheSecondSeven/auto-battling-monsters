<?php $this->extend('../layout/dashboard'); ?>
<div class="quests form">
<h2><?php echo __('Quest'); ?></h2>
    <div class="mb-3">
		<?= $this->Html->link(__('Update Quest'), ['action' => 'update', $quest->id], ['class'=>'btn btn-primary']); ?>
	</div>
	<table class="table">
		<thead>
			<tr>
				<th>Parent Quest</th>
				<th>Title</th>
				<th>Description</th>
				<th>Required Rest</th>
				<th>Persistent</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?= (!empty($quest->parent_quest->id) ? $quest->parent_quest->title : 'None') ?></td>
				<td><?= $quest->title ?></td>
				<td><?= $quest->description ?></td>
                <td><?= $quest->get('required_rest_verbose') ?></td>
                <td><?= ($quest->persistent ? 'Yes' : 'No') ?></td>
			</tr>
		</tbody>
	</table>
	<br>
	<br>
<div class="related">
	<h3><?php echo __('Monsters'); ?><?= $this->Html->link(__('Add Monster'), ['action' => 'add-quest-monster', $quest->id], ['class'=>'btn btn-primary','style' => 'float:right;']); ?></h3>
	 
	<table class="table table-striped">
	<tr>
		<th>Name</th>
		<th>Skills</th>
		<th>Actions</th>
	</tr>
	<?php foreach ($quest->quest_monsters as $monster):?>
		<tr>
            <td><?= $monster->name ?></td>
            <td><?= $monster->get('listOfAbilities') ?></td>
			<td class="dropdown">
				<div class="dropdown">
					<button class="btn btn-primary" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
						<?php echo $this->Html->icon('pencil-fill'); ?>
					</button>
					<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
						<?php
						echo $this->Html->link(__('Update'), ['action' => 'update-quest-monster', $quest->id, $monster->id], ['class'=>'dropdown-item']);
						echo $this->Html->link(__('Delete'), ['action' => 'delete-quest-monster', $quest->id, $monster->id], ['class'=>'dropdown-item']);
						?>
					</ul>
				</div>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>

	<h3><?php echo __('Rewards'); ?><?= $this->Html->link(__('Add Reward'), ['action' => 'add-quest-reward', $quest->id], ['class'=>'btn btn-primary','style' => 'float:right;']); ?></h3>
	<table class="table table-striped">
        <tr>
            <th>Reward</th>
            <th>Usable?</th>
            <th>Actions</th>
        </tr>
	    <?php foreach ($quest->quest_rewards as $reward):?>
		<tr>
            <td><?= $reward->get('reward') ?></td>
            <td><?= ($reward->usable ? 'Yes' : 'No') ?></td>
			<td class="dropdown">
				<div class="dropdown">
					<button class="btn btn-primary" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
						<?php echo $this->Html->icon('pencil-fill'); ?>
					</button>
					<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
						<?php
						echo $this->Html->link(__('Update'), ['action' => 'update-quest-reward', $quest->id, $reward->id], ['class'=>'dropdown-item']);
						echo $this->Html->link(__('Delete'), ['action' => 'delete-quest-reward', $quest->id, $reward->id], ['class'=>'dropdown-item']);
						?>
					</ul>
				</div>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
</div>
</div>