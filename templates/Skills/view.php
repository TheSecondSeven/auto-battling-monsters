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
				<th><?php echo __('Targets'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($skill->skill_effects as $skillEffect):
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
					echo '<tr><td colspan="4">Will do the following '.round($skillEffect->amount_min).'-'.round($skillEffect->amount_max).' times:</td></tr>';
				}elseif($skillEffect->effect == 'Consume') {
					echo '<tr><td></td><td colspan="3">For each stack consumed:</td></tr>';
				}else{
					echo '<tr><td></td><td colspan="3">If this Effect succeeds it will trigger:</td></tr>';
				}
			}
			foreach($skillEffect->secondary_skill_effects as $secondarySkillEffect) { ?>
				<tr>
					<td></td>
					<td><?= $secondarySkillEffect->get('effect_verbose') ?>	</td>
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