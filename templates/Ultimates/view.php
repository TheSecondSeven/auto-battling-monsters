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
				<th><?php echo __('Targets'); ?></th>
			</tr>
		</thead>
		<tbody>
	<?php foreach ($ultimate->skill_effects as $skillEffect):
		if($skillEffect->effect != 'Random Amount') {
	?>
		<tr>
			<td colspan="2"><?= $skillEffect->get('effect_verbose') ?></td>
			<td><?php echo $skillEffect->chance; ?>%</td>
			<td><?php echo $skillEffect->targets; ?></td>
		</tr>
	<?php 
		} //end not random amount
		if(!empty($skillEffect->secondary_skill_effects)) {
			if($skillEffect->effect == 'Random Amount') {
				echo '<tr><td colspan="4">Will do the following '.$skillEffect->amount_min.' - '.$skillEffect->amount_max.' times:</td></tr>';
			}elseif($skillEffect->effect == 'Consume') {
				echo '<tr><td></td><td colspan="3">For each stack consumed:</td></tr>';
			}else{
				echo '<tr><td></td><td colspan="3">If this Effect succeeds it will trigger:</td></tr>';
			}
		}
		foreach($skillEffect->secondary_skill_effects as $secondarySkillEffect) { ?>
			<tr>
				<td></td>
				<td><?= $secondarySkillEffect->get('effect_verbose') ?></td>
				<td><?php echo $secondarySkillEffect->chance; ?>%</td>
				<td><?php echo $secondarySkillEffect->targets; ?></td>
			</tr>
		<?php
		}
		endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>
</div>