<?php $this->extend('../layout/dashboard'); ?>
<div class="mb-3">
    <h3>Transmute <?= $skill->name ?></h3>
    <p>
        Use Rune Shards to change a Skill into another random Skill of the same rarity.
        <br>The higher the rarity, the higher the cost.
        <br>You can choose what type the Skill is but the cost is doubled.
        <br><strong>You will LOSE access to <?= $skill->name ?> and gain a random <?= $skill->rarity ?> skill that you don't already have</strong>
    </p>
	<?= $this->Form->create() ?>
	<?= $this->Form->control('type', ['options' => $type_options]) ?>
    <?= $this->Form->submit(__('Transmute!')); ?>
    <?= $this->Form->end() ?>
</div>