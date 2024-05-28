<?php $this->extend('../layout/TwitterBootstrap/dashboard'); ?>
<div class="gauntlet_runs index">
	<h2><?php echo __('Completed Gauntlet Runs'); ?></h2>
	<table  class="table table-striped">
	<thead>
	<tr>
			<th><?php echo $this->Paginator->sort('monster_id'); ?></th>
			<th style="text-align: center;"><?php echo $this->Paginator->sort('wins'); ?></th>
			<th style="text-align: center;"><?php echo $this->Paginator->sort('losses'); ?></th>
			<th style="text-align: center;"><?php echo $this->Paginator->sort('ties'); ?></th>
			<th style="text-align: center;"><?php echo $this->Paginator->sort('created', 'Completed'); ?></th>
			<th class="actions"></th>
	</tr>
	</thead>
	<tbody>
	<?php 
		foreach ($gauntlet_runs as $gauntlet_run): ?>
	<tr>
		<td><?php echo $gauntlet_run->monster->name; ?></td>
		<td style="text-align: center;"><?php echo $gauntlet_run->wins; ?>&nbsp;</td>
		<td style="text-align: center;"><?php echo $gauntlet_run->losses; ?>&nbsp;</td>
		<td style="text-align: center;"><?php echo $gauntlet_run->ties; ?>&nbsp;</td>
		<td style="text-align: center;"><?php echo $gauntlet_run->created->format('F jS, Y g:ia'); ?></td>
		<td class="actions">
			<?php echo $this->Html->link(__('View Battles'), array('controller' => 'gauntlet_runs', 'action' => 'view_battles', $gauntlet_run->id, 'admin' => false)); ?>
			<?php echo $this->Html->link(__('View Results'), array('controller' => 'gauntlet_runs', 'action' => 'view_results', $gauntlet_run->id, 'admin' => false)); ?>
		</td>
	</tr>
    <?php if(!empty($gauntlet_run->skill1->id)) { ?>
	<tr>
		<td colspan="7"> 
            <?php
			echo '<span style="display:inline-block;width:20%;text-align:center;">'.$gauntlet_run->skill1->name;
			echo '</span>';
			echo '<span style="display:inline-block;width:20%;text-align:center;">'.$gauntlet_run->skill2->name;
			echo '</span>';
			echo '<span style="display:inline-block;width:20%;text-align:center;">'.$gauntlet_run->skill3->name;
			echo '</span>';
			echo '<span style="display:inline-block;width:20%;text-align:center;">'.$gauntlet_run->skill4->name;
			echo '</span>';
			echo '<span style="display:inline-block;width:20%;text-align:center;">'.$gauntlet_run->ultimate->name;
			echo '</span>'; 
            ?>
		</td>
	</tr>
    <?php }
    if(!empty($gauntlet_run->rune1->id)) { ?>
	<tr>
		<td colspan="7"> 
            <?php
			echo '<span style="display:inline-block;width:20%;text-align:center;"></span>';
			echo '<span style="display:inline-block;width:20%;text-align:center;">'.(!empty($gauntlet_run->rune1->id) ? 'Level '.$gauntlet_run->rune1->level.' '.$gauntlet_run->rune1->type->name.' Rune' : '').'</span>';
			echo '<span style="display:inline-block;width:20%;text-align:center;">'.(!empty($gauntlet_run->rune2->id) ? 'Level '.$gauntlet_run->rune2->level.' '.$gauntlet_run->rune2->type->name.' Rune' : '').'</span>';
			echo '<span style="display:inline-block;width:20%;text-align:center;">'.(!empty($gauntlet_run->rune3->id) ? 'Level '.$gauntlet_run->rune3->level.' '.$gauntlet_run->rune3->type->name.' Rune' : '').'</span>';
			echo '<span style="display:inline-block;width:20%;text-align:center;"></span>';
            ?>
		</td>
	</tr>
    <?php } ?>
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
