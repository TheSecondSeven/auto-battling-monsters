<div class="skills view">
<h2><?php echo __('Ultimate'); ?></h2>
	<dl>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($ultimate['Ultimate']['name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Type'); ?></dt>
		<dd>
			<?php echo $ultimate['Type']['name']; ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Description'); ?></dt>
		<dd>
			<?php echo h($ultimate['Ultimate']['description']); ?>
			&nbsp;
		</dd>
		<?php if($ultimate['Ultimate']['passive']) { ?>
		<dt><?php echo __('Passive'); ?></dt>
		<dd>
			Yes
			&nbsp;
		</dd>
		<?php }else{ ?>
		<dt><?php echo __('Starts with How Many Charges'); ?></dt>
		<dd>
			<?php echo $ultimate['Ultimate']['starting_charges']; ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Charges Needed'); ?></dt>
		<dd>
			<?php echo $ultimate['Ultimate']['charges_needed']; ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Cast Time'); ?></dt>
		<dd>
			<?php if($ultimate['Ultimate']['cast_time'] == 0.00) { echo 'Instant'; }else{ echo h($ultimate['Ultimate']['cast_time']); } ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Down Time'); ?></dt>
		<dd>
			<?php if($ultimate['Ultimate']['down_time'] == 0.00) { echo 'None'; }else{ echo h($ultimate['Ultimate']['down_time']); } ?>
			&nbsp;
		</dd>
		<?php } ?>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('My Stuff'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('My Monsters'), array('controller' => 'users', 'action' => 'my_monsters')); ?> </li>
		<li><?php echo $this->Html->link(__('My Skills'), array('controller' => 'users', 'action' => 'my_skills')); ?> </li>
		<li><?php echo $this->Html->link(__('My Ultimates'), array('controller' => 'users', 'action' => 'my_ultimates')); ?> </li>
		<li><?php echo $this->Html->link(__('My Runes'), array('controller' => 'users', 'action' => 'my_augments')); ?> </li>
	</ul>
</div>
<?php if (!empty($ultimate['SkillEffect'])): ?>
<div class="related">
	<h3><?php echo __('Effects'); ?></h3>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th colspan="2"><?php echo __('Effect'); ?></th>
		<th><?php echo __('Chance'); ?></th>
		<th><?php echo __('Amount'); ?></th>
		<th><?php echo __('Targets'); ?></th>
		<th><?php echo __('Duration'); ?></th>
	</tr>
	<?php foreach ($ultimate['SkillEffect'] as $skillEffect):
		if($skillEffect['effect'] != 'Random Amount') {
	?>
		<tr>
			<td colspan="2"><?php echo $skillEffect['effect']; if($skillEffect['effect'] == 'Consume') { echo ' Status: '.$status_options[$skillEffect['status']]; } ?></td>
			<td><?php echo $skillEffect['chance']; ?>%</td>
			<td><?php if($skillEffect['amount_min'] != 0.00 || $skillEffect['amount_max'] != 0.00) { if($skillEffect['amount_min'] == $skillEffect['amount_max']) { echo $skillEffect['amount_min']; }else{ echo $skillEffect['amount_min'].'-'.$skillEffect['amount_max']; } }else{ echo 'N/A'; } ?></td>
			<td><?php echo $skillEffect['targets']; ?></td>
			<td><?php if($skillEffect['duration'] == 0) { echo 'N/A'; }else{ echo $skillEffect['duration']; } ?></td>
		</tr>
	<?php 
		} //end not random amount
		if(!empty($skillEffect['SecondarySkillEffect'])) {
			if($skillEffect['effect'] == 'Random Amount') {
				echo '<tr><td></td><td colspan="5">Will do the following '.$skillEffect['amount_min'].' - '.$skillEffect['amount_max'].' times:</td></tr>';
			}elseif($skillEffect['effect'] == 'Consume') {
				echo '<tr><td></td><td colspan="5">For each stack consumed:</td></tr>';
			}else{
				echo '<tr><td></td><td colspan="5">If this Effect succeeds it will trigger:</td></tr>';
			}
		}
		foreach($skillEffect['SecondarySkillEffect'] as $secondarySkillEffect) { ?>
			<tr>
				<td></td>
				<td><?php echo $secondarySkillEffect['effect']; ?></td>
				<td><?php echo $secondarySkillEffect['chance']; ?>%</td>
				<td><?php if($secondarySkillEffect['amount_min'] != 0.00 || $secondarySkillEffect['amount_min'] != 0.00) { if($secondarySkillEffect['amount_min'] == $secondarySkillEffect['amount_max']) { echo $secondarySkillEffect['amount_min']; }else{ echo $secondarySkillEffect['amount_min'].'-'.$secondarySkillEffect['amount_max']; } }else{ echo 'N/A'; } ?></td>
				<td><?php echo $secondarySkillEffect['targets']; ?></td>
				<td><?php if($secondarySkillEffect['duration'] == 0) { echo 'N/A'; }else{ echo $secondarySkillEffect['duration']; } ?></td>
			</tr>
		<?php
		}
		endforeach; ?>
	</table>
</div>
<?php endif; ?>

