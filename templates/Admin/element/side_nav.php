<div class="actions">
	<div class="list-group">
		<?= $this->Html->link('Monsters', ['controller' => 'monsters', 'action' => 'index'], ['class'=>'list-group-item list-group-item-action']); ?>
		<?= $this->Html->link('Skills', ['controller' => 'skills', 'action' => 'index'], ['class'=>'list-group-item list-group-item-action']); ?> </li>
		<?= $this->Html->link('Ultimates', ['controller' => 'ultimates', 'action' => 'index'], ['class'=>'list-group-item list-group-item-action']); ?> </li>
        <br>
		<?= $this->Html->link('Front End', '/', ['class'=>'list-group-item list-group-item-action']); ?>
	</ul>
</div>