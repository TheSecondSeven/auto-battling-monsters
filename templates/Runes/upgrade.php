<?php $this->extend('../layout/dashboard'); ?>
<div class="runes form">
    <h3>Upgrade Rune</h3>
    <?php if($rune->get('current_level') < $rune->level) { ?>
    <p>Your level <?= $rune->level ?> Rune still has <?= ($rune->level - $rune->get('current_level')) ?> free upgrade<?= ($rune->level - $rune->get('current_level') > 1 ? 's' : '') ?>!
    <?php }else{ ?>
    <p>Adding another level to this Rune will cost <?= $cost ?> rune shards.</p>
    <?php } ?>
    <p></p>
	<?= $this->Form->create($rune) ?>
	<?= $this->Form->control('id'); ?>
	<?= $this->Form->control('upgrade', ['options' => $upgrade_options]); ?>
    <?= $this->Form->submit('Upgrade'.($cost > 0 ? ' ('.$cost.' Rune Shards)' : '')); ?>
    <?= $this->Form->end() ?>
</div>