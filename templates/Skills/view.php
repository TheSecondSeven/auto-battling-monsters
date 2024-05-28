<?php $this->extend('../layout/dashboard'); ?>
<div class="skills form">
	<h2><?php echo __('Skill'); ?></h2>
	<table class="table">
		<thead>
			<tr>
				<th>Name</th>
				<th>Type</th>
				<th>Description</th>
				<th>Cast Time</th>
				<th>Down Time</th>
				<th>Owned</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?= $skill->name ?></td>
				<td><?= $skill->type->name ?></td>
				<td><?= $skill->description ?></td>
				<td><?= $skill->cast_time ?></td>
				<td><?= $skill->down_time ?></td>
				<td><?= ($skill->owned ? 'Yes' : 'No') ?></td>
			</tr>
		</tbody>
	</table>
	<?php if (!empty($skill->skill_effects)): ?>
	<br>
	<br>
	<h3><?php echo __('Effects'); ?></h3>
	<table class="table">
		<thead>
			<tr>
				<th colspan="2"><?php echo __('Effect'); ?></th>
				<th><?php echo __('Chance'); ?></th>
				<th><?php echo __('Amount'); ?></th>
				<th><?php echo __('Targets'); ?></th>
				<th><?php echo __('Duration'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($skill->skill_effects as $skillEffect):
				if($skillEffect->effect != 'Random Amount') {
			?>
			<tr>
				<td colspan="2"><?php echo $skillEffect->effect; if($skillEffect->effect == 'Consume') { echo ' Status: '.$status_options[$skillEffect->status]; } ?></td>
				<td><?php echo $skillEffect->chance; ?>%</td>
				<td><?php if($skillEffect->amount_min != 0.00 || $skillEffect->amount_max != 0.00) { if($skillEffect->amount_min == $skillEffect->amount_max) { echo $skillEffect->amount_min; }else{ echo $skillEffect->amount_min.'-'.$skillEffect->amount_max; } if(in_array($skillEffect->effect, ['Attack Up', 'Attack Down', 'Defense Up', 'Defense Down', 'Speed Up', 'Speed Down', 'Evade Up', 'Evade Down',])) echo '%'; }else{ echo 'N/A'; } ?></td>
				<td><?php echo $skillEffect->targets; ?></td>
				<td><?php if($skillEffect->duration == 0) { echo 'N/A'; }else{ echo $skillEffect->duration; } ?></td>
			</tr>
			<?php 
			} //end not random amount
			if(!empty($skillEffect->secondary_skill_effects)) {
				if($skillEffect->effect == 'Random Amount') {
					echo '<tr><td></td><td colspan="5">Will do the following '.$skillEffect->amount_min.' - '.$skillEffect->amount_max.' times:</td></tr>';
				}elseif($skillEffect->effect == 'Consume') {
					echo '<tr><td></td><td colspan="5">For each stack consumed:</td></tr>';
				}else{
					echo '<tr><td></td><td colspan="5">If this Effect succeeds it will trigger:</td></tr>';
				}
			}
			foreach($skillEffect->secondary_skill_effects as $secondarySkillEffect) { ?>
				<tr>
					<td></td>
					<td><?php echo $secondarySkillEffect->effect; ?></td>
					<td><?php echo $secondarySkillEffect->chance; ?>%</td>
					<td><?php if($secondarySkillEffect->amount_min != 0.00 || $secondarySkillEffect->amount_min != 0.00) { if($secondarySkillEffect->amount_min == $secondarySkillEffect->amount_max) { echo $secondarySkillEffect->amount_min; }else{ echo $secondarySkillEffect->amount_min.'-'.$secondarySkillEffect->amount_max; } if(in_array($secondarySkillEffect->effect, ['Attack Up', 'Attack Down', 'Defense Up', 'Defense Down', 'Speed Up', 'Speed Down', 'Evade Up', 'Evade Down',])) echo '%'; }else{ echo 'N/A'; } ?></td>
					<td><?php echo $secondarySkillEffect->targets; ?></td>
					<td><?php if($secondarySkillEffect->duration == 0) { echo 'N/A'; }else{ echo $secondarySkillEffect->duration; } ?></td>
				</tr>
			<?php
			}
			endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>
</div>