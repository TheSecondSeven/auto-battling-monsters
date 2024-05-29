<?php $this->extend('../layout/dashboard'); ?>
<div class="statuses index">
	<h2><?php echo __('Statuses'); ?></h2>
	<table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th class="actions"></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($statuses as $status): ?>
            <tr>
                <td><?= $status->name ?></td>
                <td><?= $status->description ?></td>
                <td>
                    <?= $this->Html->link(__('View'), ['action' => 'view', $status->id], ['class'=>'btn btn-primary']); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
	</table>
</div>