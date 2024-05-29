<?php $this->extend('../layout/dashboard'); ?>
<div class="skills form">
<h2><?php echo __('Skill'); ?></h2>
	<dl>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($skill->name); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Value'); ?></dt>
		<dd>
			<?php echo $skill->value; ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Type'); ?></dt>
		<dd>
			<?php echo $skill->type->name; ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Description'); ?></dt>
		<dd>
			<?php echo h($skill->description); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Cast Time'); ?></dt>
		<dd>
			<?php if($skill->cast_time == 0.00) { echo 'Instant'; }else{ echo h($skill->cast_time); } ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Down Time'); ?></dt>
		<dd>
			<?php if($skill->down_time == 0.00) { echo 'None'; }else{ echo h($skill->down_time); } ?>
			&nbsp;
		</dd>
	</dl>
	<br>
	<br>
<div class="related">
	<h3><?php echo __('Effects'); ?><?= $this->Html->link(__('Add Skill Effect'), ['action' => 'add-skill-effect', $skill->id], ['class'=>'btn btn-primary','style' => 'float:right;']); ?></h3>
	 
	<table class="table table-striped">
	<tr>
		<th colspan="2"><?php echo __('Effect'); ?></th>
		<th><?php echo __('Chance'); ?></th>
		<th><?php echo __('Amount'); ?></th>
		<th><?php echo __('Targets'); ?></th>
		<th><?php echo __('Duration'); ?></th>
		<th>Actions</th>
	</tr>
	<?php foreach ($skill->skill_effects as $skill_effect):?>
		<tr>
			<td colspan="2"><?php echo $skill_effect->effect; if($skill_effect->effect == 'Consume') { echo ' Status: '.$status_options[$skill_effect->status]; } ?></td>
			<td><?php echo $skill_effect->chance; ?>%</td>
			<td><?php if($skill_effect->amount_min != 0.00 || $skill_effect->amount_max != 0.00) { if($skill_effect->amount_min == $skill_effect->amount_max) { echo $skill_effect->amount_min; }else{ echo $skill_effect->amount_min.'-'.$skill_effect->amount_max; } }else{ echo 'N/A'; } ?></td>
			<td><?php echo $skill_effect->targets; ?></td>
			<td><?php if($skill_effect->duration == 0) { echo 'N/A'; }else{ echo $skill_effect->duration; } ?></td>
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
				echo '<tr><td></td><td colspan="6">Will do the following '.$skill_effect->amount_min.' - '.$skill_effect->amount_max.' times:</td></tr>';
			}elseif($skill_effect->effect == 'Consume') {
				echo '<tr><td></td><td colspan="6">For each stack consumed:</td></tr>';
			}else{
				echo '<tr><td></td><td colspan="6">If this Effect succeeds it will trigger:</td></tr>';
			}
		}
		foreach($skill_effect->secondary_skill_effects as $secondary_skill_effect) { ?>
			<tr>
				<td></td>
				<td><?php echo $secondary_skill_effect->effect; ?></td>
				<td><?php echo $secondary_skill_effect->chance; ?>%</td>
				<td><?php if($secondary_skill_effect->amount_min != 0.00 || $secondary_skill_effect->amount_min != 0.00) { if($secondary_skill_effect->amount_min == $secondary_skill_effect->amount_max) { echo $secondary_skill_effect->amount_min; }else{ echo $secondary_skill_effect->amount_min.'-'.$secondary_skill_effect->amount_max; } }else{ echo 'N/A'; } ?></td>
				<td><?php echo $secondary_skill_effect->targets; ?></td>
				<td><?php if($secondary_skill_effect->duration == 0) { echo 'N/A'; }else{ echo $secondary_skill_effect->duration; } ?></td>
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