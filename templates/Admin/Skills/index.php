<?php $this->extend('../layout/dashboard'); ?>
<div class="skills index">
	<h2><?php echo __('Skills'); ?><?= $this->Html->link(__('Create Skill'), ['action' => 'create'], ['class'=>'btn btn-primary','style' => 'float:right;']); ?></h2>
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
                    <th><?php echo $this->Paginator->sort('rarity'); ?></th>
                    <th><?php echo $this->Paginator->sort('value'); ?></th>
                    <th><?php echo $this->Paginator->sort('name'); ?></th>
                    <th><?php echo $this->Paginator->sort('type_id'); ?></th>
                    <th><?php echo $this->Paginator->sort('description'); ?></th>
                    <th><?php echo $this->Paginator->sort('cast_time'); ?></th>
                    <th><?php echo $this->Paginator->sort('down_time'); ?></th>
                    <th class="actions"><?php echo __('Actions'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($skills as $skill): ?>
            <tr>
                <td><?php echo h($skill->rarity); ?>&nbsp;</td>
                <td><?php echo h($skill->value); ?>&nbsp;</td>
                <td><?php echo h($skill->name); ?>&nbsp;</td>
                <td>
                    <?php echo $skill->type->name; ?>
                </td>
                <td><?php echo h($skill->description); ?>&nbsp;</td>
                <td><?php echo h($skill->cast_time); ?>&nbsp;</td>
                <td><?php echo h($skill->down_time); ?>&nbsp;</td>
                <td class="dropdown">
                    <div class="dropdown">
                        <button class="btn btn-primary" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo $this->Html->icon('pencil-fill'); ?>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <?php
                            echo $this->Html->link(__('View'), ['action' => 'view', $skill->id], ['class'=>'dropdown-item']);
                            echo $this->Html->link(__('Update'), ['action' => 'update', $skill->id], ['class'=>'dropdown-item']);
                            echo $this->Html->link(__('Delete'), ['action' => 'delete', $skill->id], ['class'=>'dropdown-item']);
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