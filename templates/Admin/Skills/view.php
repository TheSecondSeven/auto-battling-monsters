<?php $this->extend('../layout/dashboard'); ?>
<div class="skills form">
<h2><?php echo __('Skill'); ?></h2>
    <div class="mb-3">
		<?= $this->Html->link(__('Update Skill'), ['action' => 'update', $skill->id], ['class'=>'btn btn-primary']); ?>
	</div>
	<table class="table">
		<thead>
			<tr>
				<th>Name</th>
				<th>Value</th>
				<th>Type</th>
				<th>Description</th>
				<th>Cast Time</th>
				<th>Down Time</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?= $skill->name ?></td>
				<td><?= $skill->value ?></td>
				<td><?= $skill->type->name ?></td>
				<td><?= $skill->description ?></td>
				<td><?= $skill->cast_time ?></td>
				<td><?= $skill->down_time ?></td>
			</tr>
		</tbody>
	</table>
	<br>
	<br>
<div class="related">
	<h3><?php echo __('Effects'); ?><?= $this->Html->link(__('Add Skill Effect'), ['action' => 'add-skill-effect', $skill->id], ['class'=>'btn btn-primary','style' => 'float:right;']); ?></h3>
	 
	<table class="table table-striped">
	<tr>
		<th colspan="2"><?php echo __('Effect'); ?></th>
		<th><?php echo __('Chance'); ?></th>
		<th><?php echo __('Targets'); ?></th>
		<th>Actions</th>
	</tr>
	<?php foreach ($skill->skill_effects as $skill_effect):?>
		<tr>
			<td colspan="2"><?= $skill_effect->get('effect_verbose') ?></td>
			<td><?php echo $skill_effect->chance; ?>%</td>
			<td><?php echo $skill_effect->targets; ?></td>
			<td class="dropdown">
				<div class="dropdown">
					<button class="btn btn-primary" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
						<?php echo $this->Html->icon('pencil-fill'); ?>
					</button>
					<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
						<?php
						echo $this->Html->link(__('Add Secondary Effect'), ['action' => 'add-secondary-skill-effect', $skill->id, $skill_effect->id], ['class'=>'dropdown-item']);
						echo $this->Html->link(__('Update'), ['action' => 'update-skill-effect', $skill->id, $skill_effect->id], ['class'=>'dropdown-item']);
						echo $this->Html->link(__('Delete'), ['action' => 'delete-skill-effect', $skill->id, $skill_effect->id], ['class'=>'dropdown-item']);
						?>
					</ul>
				</div>
			</td>
		</tr>
	<?php 
		if(!empty($skill_effect->secondary_skill_effects)) {
			if($skill_effect->effect == 'Random Amount') {
				echo '<tr><td colspan="5">Will do the following '.round($skill_effect->amount_min).' - '.round($skill_effect->amount_max).' times:</td></tr>';
			}elseif($skill_effect->effect == 'Consume') {
				echo '<tr><td></td><td colspan="4">For each stack consumed:</td></tr>';
			}else{
				echo '<tr><td></td><td colspan="4">If this Effect succeeds it will trigger:</td></tr>';
			}
		}
		foreach($skill_effect->secondary_skill_effects as $secondary_skill_effect) { ?>
			<tr>
				<td></td>
				<td><?= $secondary_skill_effect->get('effect_verbose') ?></td>
				<td><?php echo $secondary_skill_effect->chance; ?>%</td>
				<td><?php echo $secondary_skill_effect->targets; ?></td>
				<td class="dropdown">
					<div class="dropdown">
						<button class="btn btn-primary" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
							<?php echo $this->Html->icon('pencil-fill'); ?>
						</button>
						<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
							<?php
							echo $this->Html->link(__('Update'), ['action' => 'update-skill-effect', $skill->id, $secondary_skill_effect->id], ['class'=>'dropdown-item']);
							echo $this->Html->link(__('Delete'), ['action' => 'delete-skill-effect', $skill->id, $secondary_skill_effect->id], ['class'=>'dropdown-item']);
							?>
						</ul>
					</div>
				</td>
			</tr>
		<?php
		}
		endforeach; ?>
	</table>
</div>
</div>