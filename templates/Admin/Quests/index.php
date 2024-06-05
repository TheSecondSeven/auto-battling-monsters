<?php $this->extend('../layout/dashboard'); ?>
<div class="quests index">
	<h2><?php echo __('Quests'); ?><?= $this->Html->link(__('Create Quest'), ['action' => 'create'], ['class'=>'btn btn-primary','style' => 'float:right;']); ?></h2>
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
            echo $this->Form->control('title', [
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
                    <th>Parent Quest</th>
                    <th>Depth</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Required Rest</th>
                    <th>Persistent</th>
                    <th>Enemies</th>
                    <th>Rewards</th>
                    <th class="actions">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($quests as $quest): ?>
            <tr>
                <td><?= (!empty($quest->parent_quest->id) ? $quest->parent_quest->title : '') ?></td>
                <td><?= $quest->depth ?></td>
                <td><?= $quest->title ?></td>
                <td><?= $quest->description ?></td>
                <td><?= $quest->get('required_rest_verbose') ?></td>
                <td><?= ($quest->persistent ? 'Yes' : 'No') ?></td>
                <td><?= count($quest->quest_monsters) ?></td>
                <td><?= count($quest->quest_rewards) ?></td>
                <td class="dropdown">
                    <div class="dropdown">
                        <button class="btn btn-primary" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo $this->Html->icon('pencil-fill'); ?>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <?php
                            echo $this->Html->link(__('View'), ['action' => 'view', $quest->id], ['class'=>'dropdown-item']);
                            echo $this->Html->link(__('Update'), ['action' => 'update', $quest->id], ['class'=>'dropdown-item']);
                            echo $this->Html->link(__('Create Child Quest'), ['action' => 'create', $quest->id], ['class'=>'dropdown-item']);
                            if(empty($quest->child_quests)) {
                                echo $this->Html->link(__('Delete'), ['action' => 'delete', $quest->id], ['class'=>'dropdown-item']);
                            }
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