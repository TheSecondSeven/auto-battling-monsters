<?php $this->extend('../layout/dashboard'); ?>
<div class="skills index">
	<h2><?php echo __('My Runes'); ?></h2>
	<?php //echo $this->Html->link('Create Rune', ['controller' => 'runes', 'action' => 'create'],['class' => 'btn btn-primary']); ?>
	<table class="table table-striped">
	<?php 
	if(count($runes) == 0) {
		echo '<tr><td colspan="11">You currently don\'t have any runes. Progress through the campaign to earn some!</td></tr>';
	}else{ ?>	
        <thead>
            <tr>
                <th><?php echo $this->Paginator->sort('type_id'); ?></th>
                <th><?php echo $this->Paginator->sort('level'); ?></th>
                <th><?php echo $this->Paginator->sort('in_use_by_monster_id', 'Used By'); ?></th>
                <th><?php echo $this->Paginator->sort('unlock_type', 'Unlock Type'); ?></th>
                <th><?php echo $this->Paginator->sort('damage_level', 'Damage Increase'); ?></th>
                <th><?php echo $this->Paginator->sort('healing_level', 'Healing Increase'); ?></th>
                <th><?php echo $this->Paginator->sort('critical_chance_level', 'Critical Chance Increase'); ?></th>
                <th><?php echo $this->Paginator->sort('cast_again_level', 'Overload Chance Increase'); ?></th>
                <th><?php echo $this->Paginator->sort('casting_speed_level', 'Cast Speed Increase'); ?></th>
                <th><?php echo $this->Paginator->sort('health_level', 'Health Increase'); ?></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($runes as $rune): ?>
            <tr>
                <td><?php echo h($rune->type->name); ?>&nbsp;</td>
                <td><?php echo h($rune->level); ?>&nbsp;</td>
                <td><?php if(!empty($rune->monster->id)) { echo $rune->monster->name; }else{ echo 'Available'; } ?>&nbsp;</td>
                <td><?php if($rune->unlock_type) { echo 'Unlocks '.$rune->type->name.' Skills'; }else{ echo 'No'; } ?></td>
                <td><?php if($rune->damage_level > 0) { echo $rune->type->name.' Damage Increased '.(RUNE_DAMAGE_INCREASE * $rune->damage_level).'%'; }else{ echo 'None'; } ?>&nbsp;</td>
                <td><?php if($rune->healing_level > 0) { echo $rune->type->name.' Healing Increased '.(RUNE_HEALING_INCREASE * $rune->healing_level).'%'; }else{ echo 'None'; } ?>&nbsp;</td>
                <td><?php if($rune->critical_chance_level > 0) { echo 'Critical Hits with '.$rune->type->name.' Abilities Increased '.(RUNE_CRITICAL_CHANCE_INCREASE * $rune->critical_chance_level).'%'; }else{ echo 'None'; } ?>&nbsp;</td>
                <td><?php if($rune->cast_again_level > 0) { echo $rune->type->name.' Skills Have a '.(RUNE_CAST_AGAIN_INCREASE * $rune->cast_again_level).'% to Cast Again'; }else{ echo 'None'; } ?>&nbsp;</td>
                <td><?php if($rune->casting_speed_level > 0) { echo 'Casting Speed of '.$rune->type->name.' Skills Increased '.(RUNE_CASTING_SPEED_INCREASE * $rune->casting_speed_level).'%'; }else{ echo 'None'; } ?>&nbsp;</td>
                <td><?php if($rune->health_level > 0) { echo 'Monster Health Increased '.(RUNE_HEALTH_INCREASE * $rune->health_level).'%'; }else{ echo 'None'; } ?>&nbsp;</td>
                <td class="actions">
                    <?php echo $this->Html->link('Upgrade', ['controller' => 'runes', 'action' => 'upgrade', $rune->id],['class' => 'btn btn-primary']); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
	<?php } ?>
	</table>
    <nav aria-label="Page navigation example">
        <ul class="pagination">
            <?php
            echo $this->Paginator->first();
            echo $this->Paginator->prev();
            echo $this->Paginator->numbers();
            echo $this->Paginator->next();
            echo $this->Paginator->last();
            ?>
        </ul>
    </nav>
</div>