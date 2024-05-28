<?php $this->extend('../layout/dashboard'); ?>
<div class="ultimate form">
	<h2><?php echo __('Ultimate'); ?></h2>
	<table class="table">
		<thead>
			<tr>
				<th>Name</th>
				<th>Type</th>
				<th>Description</th>
				<th>Passive or Charging</th>
				<th>Cast Time</th>
				<th>Down Time</th>
				<th>Owned</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?= $ultimate->name ?></td>
				<td><?= $ultimate->type->name ?><?php if(!empty($ultimate->secondary_type->id) && $ultimate->type->id != $ultimate->secondary_type->id) echo '/'.$ultimate->secondary_type->name; ?></td>
				<td><?= $ultimate->description ?></td>
                <td><?php if($ultimate->passive) { echo 'Passive'; }else{ echo 'Starts with '.$ultimate->starting_charges.'/'.$ultimate->charges_needed.' Charge'.($ultimate->charges_needed == 1 ? '' : 's').' Needed'; } ?></td>
				<td><?= $ultimate->cast_time ?></td>
				<td><?= $ultimate->down_time ?></td>
				<td><?= ($ultimate->owned ? 'Yes' : 'No') ?></td>
			</tr>
		</tbody>
	</table>
	<?php if (!empty($ultimate->skill_effects)): ?>
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
	<?php foreach ($ultimate->skill_effects as $skillEffect):
		if($skillEffect->effect != 'Random Amount') {
	?>
		<tr>
			<td colspan="2"><?php echo $skillEffect->effect; if($skillEffect->effect == 'Consume') { echo ' Status: '.$status_options[$skillEffect->status]; } ?></td>
			<td><?php echo $skillEffect->chance; ?>%</td>
			<td><?php if($skillEffect->amount_min != 0.00 || $skillEffect->amount_max != 0.00) { if($skillEffect->amount_min == $skillEffect->amount_max) { echo $skillEffect->amount_min; }else{ echo $skillEffect->amount_min.'-'.$skillEffect->amount_max; } }else{ echo 'N/A'; } ?></td>
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
				<td><?php if($secondarySkillEffect->amount_min != 0.00 || $secondarySkillEffect->amount_min != 0.00) { if($secondarySkillEffect->amount_min == $secondarySkillEffect->amount_max) { echo $secondarySkillEffect->amount_min; }else{ echo $secondarySkillEffect->amount_min.'-'.$secondarySkillEffect->amount_max; } }else{ echo 'N/A'; } ?></td>
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