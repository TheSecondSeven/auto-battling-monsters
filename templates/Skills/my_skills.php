<?php $this->extend('../layout/TwitterBootstrap/dashboard'); ?>
<div class="skills index">
	<h2><?php echo __('My Skills'); ?></h2>
	<table class="table table-striped">
        <thead>
        <tr>
            <th>Used By</th>
            <th><?php echo $this->Paginator->sort('rarity'); ?></th>
            <th><?php echo $this->Paginator->sort('name'); ?></th>
            <th><?php echo $this->Paginator->sort('type_id'); ?></th>
            <th><?php echo $this->Paginator->sort('description'); ?></th>
            <th><?php echo $this->Paginator->sort('cast_time'); ?></th>
            <th><?php echo $this->Paginator->sort('down_time'); ?></th>
            <th class="actions"></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($skills as $skill): ?>
            <tr>
                <td><?php 
                    if(!empty($skill->monster1[0])) echo $skill->monster1[0]->name;
                    if(!empty($skill->monster2[0])) echo $skill->monster2[0]->name;
                    if(!empty($skill->monster3[0])) echo $skill->monster3[0]->name;
                    if(!empty($skill->monster4[0])) echo $skill->monster4[0]->name;
                    ?>
                </td>
                <td><?php echo h($skill->rarity); ?>&nbsp;</td>
                <td><?php echo h($skill->name); ?>&nbsp;</td>
                <td><?php echo $skill->type->name; ?>&nbsp;</td>
                <td><?php echo h($skill->description); ?>&nbsp;</td>
                <td><?php if($skill->cast_time == 0.00) { echo 'Instant'; }else{ echo h($skill->cast_time); } ?>&nbsp;</td>
                <td><?php if($skill->down_time == 0.00) { echo 'None'; }else{ echo h($skill->down_time); } ?>&nbsp;</td>
                <td class="actions">
                    <?= $this->Html->link('View Details', ['controller' => 'skills', 'action' => 'view', $skill->id], ['class' => 'btn btn-primary']); ?>
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