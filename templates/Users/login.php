<?php 
use BootstrapUI\View\Helper\FormHelper;
$this->extend('../layout/TwitterBootstrap/signin'); ?>
<div class="users form">
    <?= $this->Flash->render('', [
    'align' => [
        // column sizes for the `sm` screen-size/breakpoint
        'sm' => [
            FormHelper::GRID_COLUMN_ONE => 6,
            FormHelper::GRID_COLUMN_TWO => 6,
        ],
        // column sizes for the `md` screen-size/breakpoint
        'md' => [
            FormHelper::GRID_COLUMN_ONE => 4,
            FormHelper::GRID_COLUMN_TWO => 8,
        ],
    ],
]) ?>
    <h3>Login</h3>
    <?= $this->Form->create() ?>
    <?= $this->Form->control('email', ['required' => true]) ?>
    <?= $this->Form->control('password', ['required' => true]) ?>
    <?= $this->Form->control('remember_me', ['type' => 'checkbox']);?>
    <?= $this->Form->submit(__('Login'),['class'=>'btn btn-primary']); ?>
    <br>
    <?= $this->Html->link('Register', ['action' => 'register'],['class'=>'btn btn-secondary']) ?>
    <?= $this->Form->end() ?>

</div>