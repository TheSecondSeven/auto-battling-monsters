<?php $this->extend('../layout/dashboard'); ?>
<div class="skills index">
	<h2><?php echo __('My Skills'); ?><?= $this->Html->link(__('View All Skills'), ['action' => 'index'], ['class'=>'btn btn-primary','style' => 'float:right;']); ?></h2>
	<table class="table table-striped" style="table-layout:fixed;">
        <thead>
        <tr>
            <th><?php echo $this->Paginator->sort('rarity'); ?></th>
            <th><?php echo $this->Paginator->sort('name'); ?></th>
            <th><?php echo $this->Paginator->sort('type_id'); ?></th>
            <th><?php echo $this->Paginator->sort('description'); ?></th>
            <th><?php echo $this->Paginator->sort('cast_time'); ?></th>
            <th><?php echo $this->Paginator->sort('down_time'); ?></th>
            <th>In Use By</th>
            <th class="actions"></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($skills as $skill): ?>
            <tr>
                <td><?php echo h($skill->rarity); ?>&nbsp;</td>
                <td><?php echo h($skill->name); ?>&nbsp;</td>
                <td><?php echo $skill->type->name; ?>&nbsp;</td>
                <td style="white-space: nowrap; overflow: hidden; text-overflow:ellipsis;"><?php echo h($skill->description); ?>&nbsp;</td>
                <td><?php if($skill->cast_time == 0.00) { echo 'Instant'; }else{ echo h($skill->cast_time); } ?>&nbsp;</td>
                <td><?php if($skill->down_time == 0.00) { echo 'None'; }else{ echo h($skill->down_time); } ?>&nbsp;</td>
                <td><?php 
                    if(!empty($skill->monster1[0])) echo $this->Html->link($skill->monster1[0]->name, ['controller' => 'monsters', 'action' => 'edit-move-set', $skill->monster1[0]->id], ['class'=>'btn btn-success']);
                    if(!empty($skill->monster2[0])) echo $this->Html->link($skill->monster2[0]->name, ['controller' => 'monsters', 'action' => 'edit-move-set', $skill->monster2[0]->id], ['class'=>'btn btn-success']);
                    if(!empty($skill->monster3[0])) echo $this->Html->link($skill->monster3[0]->name, ['controller' => 'monsters', 'action' => 'edit-move-set', $skill->monster3[0]->id], ['class'=>'btn btn-success']);
                    if(!empty($skill->monster4[0])) echo $this->Html->link($skill->monster4[0]->name, ['controller' => 'monsters', 'action' => 'edit-move-set', $skill->monster4[0]->id], ['class'=>'btn btn-success']);
                    
                    if(empty($skill->monster1) && empty($skill->monster2) && empty($skill->monster3) && empty($skill->monster4)) {
                        echo $this->Html->link('Transmute', ['controller' => 'skills', 'action' => 'transmute', $skill->id], ['class'=>'btn btn-info', 'data-bs-toggle'=>"tooltip", 'data-bs-placement' => "top", 'title' => "Transmutate this Skill into a different ".$skill->rarity." Skill"]);
                    } ?>
                </td>
                <td><?= $this->Html->link('View Details', ['controller' => 'skills', 'action' => 'view', $skill->id], ['class'=>'btn btn-primary']) ?></td>
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