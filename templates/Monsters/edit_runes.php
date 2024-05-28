<?php $this->extend('../layout/TwitterBootstrap/dashboard'); ?>
<div class="monsters form">
    <h3>Edit Monster Skills</h3>
    <?php if($monster->rune_level < 3) {
		echo $this->Html->link(__('Purchase Another Rune Slot ('.($monster->rune_level == 1 ? 250 : 1000).' Gold)'), ['controller' => 'monsters', 'action' => 'increase_rune_level', $monster->id],['class' => 'btn btn-primary']);
	} ?>
	<?= $this->Form->create($monster) ?>
	<?= $this->Form->control('id'); ?>
	<?= $this->Form->control('rune_1_id', ['label' => '1st Rune', 'options' => $runes]); ?>
    <?php
    if($monster->rune_level > 1) {
		echo $this->Form->control('rune_2_id', ['label' => '2nd Rune', 'options' => $runes]);
    }else{
        echo $this->Form->hidden('rune_2_id');
    }
    if($monster->rune_level > 2) {
		echo $this->Form->control('rune_3_id', ['label' => '3rd Rune', 'options' => $runes]);
    }else{
        echo $this->Form->hidden('rune_3_id');
    }
    ?>
    <?= $this->Form->submit('Save Runes'); ?>
    <?= $this->Form->end() ?>
</div>