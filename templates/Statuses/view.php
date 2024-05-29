<?php $this->extend('../layout/dashboard'); ?>
<div class="skills form">
	<h2><?php echo __('Status'); ?></h2>
	<table class="table">
		<thead>
			<tr>
				<th>Name</th>
				<th>Description</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?= $status->name ?></td>
				<td><?= $status->description ?></td>
			</tr>
		</tbody>
	</table>
	<?php if (!empty($status->skills)): ?>
	<br>
	<br>
	<h3><?php echo __('Related Skills'); ?></h3>
	<table class="table table-striped">
        <thead>
            <tr>
                <th>Rarity</th>
                <th>Name</th>
                <th>Type</th>
                <th>Description</th>
                <th class="actions"></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($status->skills as $skill): ?>
            <tr>
                <td><?php echo h($skill->rarity); ?>&nbsp;</td>
                <td><?php echo h($skill->name); ?>&nbsp;</td>
                <td>
                    <?php echo $skill->type->name; ?>
                </td>
                <td><?php echo h($skill->description); ?>&nbsp;</td>
                <td>
                    <?= $this->Html->link(__('View'), ['controller'=>'skills','action' => 'view', $skill->id], ['class'=>'btn btn-primary']); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
	</table>
<?php endif; ?>
</div>