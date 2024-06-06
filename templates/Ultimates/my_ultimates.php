<?php $this->extend('../layout/dashboard'); ?>
<div class="skills index">
	<h2><?php echo __('My Ultimates'); ?></h2>
	<table class="table table-striped">
        <thead>
            <tr>
                <th></th>
                <th><?php echo $this->Paginator->sort('rarity'); ?></th>
                <th><?php echo $this->Paginator->sort('name'); ?></th>
                <th><?php echo $this->Paginator->sort('type_id'); ?></th>
                <th><?php echo $this->Paginator->sort('description'); ?></th>
                <th>Passive or Charging</th>
                <th><?php echo $this->Paginator->sort('cast_time'); ?></th>
                <th><?php echo $this->Paginator->sort('down_time'); ?></th>
                <th>In Use By</th>
                <th class="actions"></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($ultimates as $ultimate): ?>
            <tr style="vertical-align: middle;">
                <td><?= ($ultimate->_matchingData['UserUltimates']->new ? '<div class="alert alert-primary alert-label" role="alert">New</div>' : '') ?> </td>
                <td><?php echo h($ultimate->rarity); ?>&nbsp;</td>
                <td><?php echo h($ultimate->name); ?>&nbsp;</td>
                <td><?php echo $ultimate->type->name.'/'.$ultimate->secondary_type->name; ?>&nbsp;</td>
                <td><?php echo h($ultimate->description); ?>&nbsp;</td>
                <td><?php if($ultimate->passive) { echo 'Passive'; }else{ echo 'Starts with '.$ultimate->starting_charges.'/'.$ultimate->charges_needed.' Charge'.($ultimate->charges_needed == 1 ? '' : 's').' Needed'; } ?></td>
                <td><?php if($ultimate->passive) { echo 'N/A'; }else{ echo h($ultimate->cast_time); } ?>&nbsp;</td>
                <td><?php if($ultimate->passive) { echo 'N/A'; }else{ echo h($ultimate->down_time); } ?>&nbsp;</td>
                <td><?php if(!empty($ultimate->monsters[0]->id)) echo $this->Html->link($ultimate->monsters[0]->name, ['controller' => 'monsters', 'action' => 'edit-move-set', $ultimate->monsters[0]->id], ['class'=>'btn btn-success']); ?></td>
                <td class="actions">
                    <?= $this->Html->link('View Details', ['controller' => 'ultimates', 'action' => 'view', $ultimate->id], ['class' => 'btn btn-primary']); ?>
                </td>
            </tr>
        <?php endforeach; ?>
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