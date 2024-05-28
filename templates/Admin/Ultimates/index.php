<?php $this->extend('../layout/dashboard'); ?>
<div class="ultimates index">
	<h2><?php echo __('Ultimates'); ?></h2>
	<table class="table table-striped">
        <thead>
            <tr>
                <th>Used By</th>
                <th><?php echo $this->Paginator->sort('rarity'); ?></th>
                <th><?php echo $this->Paginator->sort('name'); ?></th>
                <th><?php echo $this->Paginator->sort('type_id'); ?></th>
                <th><?php echo $this->Paginator->sort('description'); ?></th>
                <th>Charging or Passive</th>
                <th><?php echo $this->Paginator->sort('cast_time'); ?></th>
                <th><?php echo $this->Paginator->sort('down_time'); ?></th>
                <th class="actions"></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($ultimates as $ultimate): ?>
            <tr>
                <td><?php echo count($ultimate->monsters).' Monster'.(count($ultimate->monsters) ? '' : 's'); ?></td>
                <td><?php echo h($ultimate->rarity); ?>&nbsp;</td>
                <td><?php echo h($ultimate->name); ?>&nbsp;</td>
                <td><?php echo $ultimate->type->name.'/'.$ultimate->secondary_type->name; ?>&nbsp;</td>
                <td><?php echo h($ultimate->description); ?>&nbsp;</td>
                <td><?php if($ultimate->passive) { echo 'Passive'; }else{ echo 'Starts with '.$ultimate->starting_charges.'/'.$ultimate->charges_needed.' Charge'.($ultimate->charges_needed == 1 ? '' : 's').' Needed'; } ?>&nbsp;</td>
                <td><?php if($ultimate->passive) { echo 'N/A'; }else{ echo h($ultimate->cast_time); } ?>&nbsp;</td>
                <td><?php if($ultimate->passive) { echo 'N/A'; }else{ echo h($ultimate->down_time); } ?>&nbsp;</td>
                <td class="dropdown">
                    <div class="dropdown">
                        <button class="btn btn-primary" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo $this->Html->icon('pencil-fill'); ?>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <?php
                            echo $this->Html->link(__('View'), ['action' => 'view', $ultimate->id], ['class'=>'dropdown-item']);
                            echo $this->Html->link(__('Update'), ['action' => 'update', $ultimate->id], ['class'=>'dropdown-item']);
                            echo $this->Html->link(__('Delete'), ['action' => 'delete', $ultimate->id], ['class'=>'dropdown-item']);
                            ?>
                        </ul>
                    </div>
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