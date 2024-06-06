<?php $this->extend('../layout/dashboard'); ?>
<div class="skills index">
	<h2><?php echo __('My Skills'); ?></h2>
	 <?php 
    $filter_count = 0;
    foreach($this->request->getQueryParams() as $key=>$value) {
      if(!in_array($key,['sort','direction']) && !empty($value)) $filter_count++;
    }
    ?>
    <div class="mb-3">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filters">
            Filter
            <?= $filter_count ? $this->Html->tag('li', $filter_count, ['class' => 'badge rounded-pill bg-secondary']) : '' ?>
        </button>
        <?= $this->Html->link(__('View All Skills'), ['action' => 'index'], ['class'=>'btn btn-primary']); ?>
    </div>
    <div id="filters" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Filters</h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <?php
            echo $this->Form->create(null, [
            'type' => 'get',
            'valueSources' => 'query']);
            echo $this->Form->control('rarity', [
                'class' => 'form-control',
                'options' => $rarities,
                'empty' => true]);
            echo $this->Form->control('type_id', [
                'class' => 'form-control',
                'options' => $types,
                'empty' => true]);
            echo $this->Form->control('name', [
                'class' => 'form-control',
                'empty' => true]);
            echo $this->Form->button('Go', [
            'class' => 'btn btn-primary',
            'escapeTitle' => false]);
            echo $this->Form->end();
            ?>
        </div>
        </div>
    </div>
    </div>
    <table class="table table-striped">
        <thead>
        <tr>
            <th></th>
            <th><?php echo $this->Paginator->sort('rarity'); ?></th>
            <th><?php echo $this->Paginator->sort('name'); ?></th>
            <th><?php echo $this->Paginator->sort('type_id'); ?></th>
            <th>Description</th>
            <th><?php echo $this->Paginator->sort('cast_time'); ?></th>
            <th><?php echo $this->Paginator->sort('down_time'); ?></th>
            <th>In Use By</th>
            <th class="actions"></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($skills as $skill): ?>
            <tr style="vertical-align: middle;">
                <td><?= ($skill->_matchingData['UserSkills']->new ? '<div class="alert alert-primary alert-label" role="alert">New</div>' : '') ?> </td>
                <td><?php echo h($skill->rarity); ?>&nbsp;</td>
                <td><?php echo h($skill->name); ?>&nbsp;</td>
                <td><?php echo $skill->type->name; ?>&nbsp;</td>
                <td class="description"><?php echo h($skill->description); ?>&nbsp;</td>
                <td><?php if($skill->cast_time == 0.00) { echo 'Instant'; }else{ echo h($skill->cast_time); } ?>&nbsp;</td>
                <td><?php if($skill->down_time == 0.00) { echo 'None'; }else{ echo h($skill->down_time); } ?>&nbsp;</td>
                <td><?php 
                    if(!empty($skill->monster1[0])) echo $this->Html->link($skill->monster1[0]->name, ['controller' => 'monsters', 'action' => 'edit-move-set', $skill->monster1[0]->id], ['class'=>'btn btn-success']);
                    if(!empty($skill->monster2[0])) echo $this->Html->link($skill->monster2[0]->name, ['controller' => 'monsters', 'action' => 'edit-move-set', $skill->monster2[0]->id], ['class'=>'btn btn-success']);
                    if(!empty($skill->monster3[0])) echo $this->Html->link($skill->monster3[0]->name, ['controller' => 'monsters', 'action' => 'edit-move-set', $skill->monster3[0]->id], ['class'=>'btn btn-success']);
                    if(!empty($skill->monster4[0])) echo $this->Html->link($skill->monster4[0]->name, ['controller' => 'monsters', 'action' => 'edit-move-set', $skill->monster4[0]->id], ['class'=>'btn btn-success']);
                    
                    if(empty($skill->monster1) && empty($skill->monster2) && empty($skill->monster3) && empty($skill->monster4) && $skill->rarity != 'Common') {
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