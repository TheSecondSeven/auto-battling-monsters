<?php $this->extend('../layout/TwitterBootstrap/dashboard'); ?>
<div class="skills index">
	<h2><?php echo __('My Runes'); ?></h2>
	<table class="table table-striped">
	<?php 
	if(empty($augments)) {
		echo '<tr><td>You have not unlocked any Runes yet.</td></tr>';
	}else{ ?>	
	<thead>
	<tr>
		<th><?php echo $this->Paginator->sort('rarity'); ?></th>
		<th><?php echo $this->Paginator->sort('type'); ?></th>
		<th><?php echo $this->Paginator->sort('name'); ?></th>
		<th><?php echo $this->Paginator->sort('description'); ?></th>
		<th><?php echo $this->Paginator->sort('skill_1', '1st Skill'); ?></th>
		<th><?php echo $this->Paginator->sort('skill_2', '2nd Skill'); ?></th>
		<th><?php echo $this->Paginator->sort('skill_3', '3rd Skill'); ?></th>
		<th><?php echo $this->Paginator->sort('skill_4', '4th Skill'); ?></th>
		<th><?php echo $this->Paginator->sort('ultimate'); ?></th>
	</tr>
	</thead>
        <tbody>
        <?php foreach ($augments as $augment): ?>
        <tr>
            <td><?php echo h($augment->rarity); ?>&nbsp;</td>
            <td><?php if($augment->type == 'Type') {
                echo 'Unlock Type';
            }elseif($augment->type == 'Damage') {
                echo 'Damage Increase';
            }elseif($augment->type == 'Healing') {
                echo 'Healing Increase';
            }elseif($augment->type == 'Chance To Cast Again') {
                echo 'Recast';
            }
                ; ?>&nbsp;</td>
            <td><?php echo h($augment->name); ?>&nbsp;</td>
            <td><?php echo h($augment->description); ?>&nbsp;</td>
            <td><?php if($augment->skill_1) { echo '✓'; }else{ echo ''; } ?>&nbsp;</td>
            <td><?php if($augment->skill_2) { echo '✓'; }else{ echo ''; } ?>&nbsp;</td>
            <td><?php if($augment->skill_3) { echo '✓'; }else{ echo ''; } ?>&nbsp;</td>
            <td><?php if($augment->skill_4) { echo '✓'; }else{ echo ''; } ?>&nbsp;</td>
            <td><?php if($augment->ultimate) { echo '✓'; }else{ echo ''; } ?>&nbsp;</td>
        </tr>
        <?php endforeach;
        } ?>
        </tbody>
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