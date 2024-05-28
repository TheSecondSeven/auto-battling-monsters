<?php $this->extend('../layout/dashboard'); ?>
<div class="skills index">
	<h2><?php echo __('My Ultimates'); ?></h2>
	<table class="table table-striped">
        <thead>
            <tr>
                <th>In Use By</th>
                <th><?php echo $this->Paginator->sort('rarity'); ?></th>
                <th><?php echo $this->Paginator->sort('name'); ?></th>
                <th><?php echo $this->Paginator->sort('type_id'); ?></th>
                <th><?php echo $this->Paginator->sort('description'); ?></th>
                <th><?php echo $this->Paginator->sort('passive'); ?></th>
                <th><?php echo $this->Paginator->sort('cast_time'); ?></th>
                <th><?php echo $this->Paginator->sort('down_time'); ?></th>
                <th class="actions"></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($ultimates as $ultimate): ?>
            <tr>
                <td><?php if(!empty($ultimate->monsters[0]->id)) echo $ultimate->monsters[0]->name; ?></td>
                <td><?php echo h($ultimate->rarity); ?>&nbsp;</td>
                <td><?php echo h($ultimate->name); ?>&nbsp;</td>
                <td><?php echo $ultimate->type->name.'/'.$ultimate->secondary_type->name; ?>&nbsp;</td>
                <td><?php echo h($ultimate->description); ?>&nbsp;</td>
                <td><?php if($ultimate->passive) { echo 'Yes'; }else{ echo 'No'; } ?>&nbsp;</td>
                <td><?php if($ultimate->passive) { echo 'N/A'; }else{ echo h($ultimate->cast_time); } ?>&nbsp;</td>
                <td><?php if($ultimate->passive) { echo 'N/A'; }else{ echo h($ultimate->down_time); } ?>&nbsp;</td>
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