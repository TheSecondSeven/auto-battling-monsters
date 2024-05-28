<?php $this->extend('../layout/dashboard'); ?>
<div class="monsters form">
    <h3>Edit <?= $monster->name.'\''.(substr($monster->name, -1) == 's' ? '' : 's') ?> Move Set</h3>
	<?= $this->Form->create($monster) ?>
	<?= $this->Form->control('id'); ?>
	<?= $this->Form->control('skill_1_id', ['label' => '1st Skill', 'options' => $skill_options]); ?>
	<?= $this->Form->control('skill_2_id', ['label' => '2nd Skill', 'options' => $skill_options]); ?>
	<?= $this->Form->control('skill_3_id', ['label' => '3rd Skill', 'options' => $skill_options]); ?>
	<?= $this->Form->control('skill_4_id', ['label' => '4th Skill', 'options' => $skill_options]); ?>
	<?= $this->Form->control('ultimate_id', ['label' => 'Ultimate', 'options' => $ultimate_options]); ?>
    <?= $this->Form->submit('Save Move Set'); ?>
    <?= $this->Form->end() ?>
    <br>
	<h2>All Moves This Monster Could Use</h2>
	<table class="table table-striped">
        <thead>
            <tr>
				<th>Move Type</th>
				<th>Owned?</th>
                <th>Rarity</th>
                <th>Name</th>
                <th>Type</th>
                <th>Description</th>
                <th>Cast Time</th>
                <th>Down Time</th>
                <th class="actions"></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($all_moves as $move): ?>
            <tr>
                <td><?= $move->move_type ?></td>
                <td><?= ($move->owned ? 'Yes' : 'No') ?></td>
                <td><?= $move->rarity ?></td>
                <td><?= $move->name ?></td>
                <td><?php echo $move->type->name; if(!empty($move->secondary_type->id)) echo '/'.$move->secondary_type->name; ?></td>
                <td><?= $move->description ?></td>
                <td><?= $move->cast_time ?></td>
                <td><?= $move->down_time ?></td>
                <td><?= $this->Html->link(__('View'), ['controller'=>$move->move_type.'s','action' => 'view', $move->id], ['class'=>'btn btn-primary']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
	</table>
</div>