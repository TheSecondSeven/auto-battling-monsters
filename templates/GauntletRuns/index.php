<?php $this->extend('../layout/dashboard'); ?>
<div class="quests form">
<h2>The Gauntlet</h2>

    <p>
        Welcome to the Gauntlet!
        <br>Here you can send you monsters off into PvP combat.
        <br>In the Gauntlet they will face off against 10 similarly rated monsters.
        <br>The monster will take <?= GAUNTLET_WAIT_TIME ?> to complete all 10 battles.
        <br>And afterwards, it the monster will rest for <?= GAUNTLET_REST_TIME ?>.
        <br>You will receive 5 gold per win and then 5 random rewards.
        <br>Higher win amounts in a single Gauntlet Run lead to guaranteed rewards of higher rarity.
    </p>
    <?php if($user->total_gauntlet_runs_today >= $user->active_monster_limit * DAILY_GAUNTLET_LIMIT_PER_ACTIVE_MONSTER) { ?>
	<div class="mb-3">
		<strong>You have completed your daily limit of Gauntlet Runs.
		<br>This limit is your "Active Monster Limit" x <?= DAILY_GAUNTLET_LIMIT_PER_ACTIVE_MONSTER ?> and resets at midnight PST.</strong>
		<?php /*if(empty($user->dreaming_since)) { ?><br>Feel free to enter Dream Mode to passively earn gold until you want to start battling again tomorrow.<?php }*/ ?>
	</div>
	<?php }elseif(count($user->monsters) >= $user->active_monster_limit) { ?>
    <div class="mb-3">
		<strong><?php echo 'You can only have '.$user->active_monster_limit.' Monster'.($user->active_monster_limit == 1 ? '' : 's').' active in the Gauntlet at a time.'; ?></strong>
	</div>
    <?php }elseif(empty($available_monsters_list)) { ?>
    <div class="mb-3">
		<strong><?php echo 'You do not have any monsters available to battle.'; ?></strong>
	</div>
    <?php }else{ ?>
    <div class="mb-3">
		<?= $this->Form->create(null, [
            'url' => [
                'controller' => 'gauntlet-runs',
                'action' => 'start-run',
            ],
        ]) ?>
        <?= $this->Form->control('monster_id', ['label' => 'Choose Your Monster','options' => $available_monsters_list]); ?>
        <?= $this->Form->submit(__('Enter the Gauntlet')); ?>
        <?= $this->Form->end() ?>
	</div>
    <?php } ?>
    <?php /*if(empty($user->dreaming_since)) { ?>
	<div class="mb-3">
		<?= $this->Html->link('Enter Dream Mode', ['controller' => 'users', 'action' => 'enter-dream-mode'], ['class'=>'btn btn-success', 'data-bs-toggle' => "tooltip", 'data-bs-placement' => "top", 'title' =>'What is Dream Mode? Click the button to read details. There\'s no downside.']); ?>					
	</div>
	<?php }else{ ?>
	<div class="mb-3">
		After 2 hours, your monsters start dreaming of gold for you. Earnings stop after 26 hours.
		<br>Each unused active monster slot will also add chances to earn rune shards or gems!
		<br>Your monsters have been dreaming for <?php $now = new DateTime();
					$past_date = $user->dreaming_since;
					
					$interval = $now->diff($past_date);
					if($interval->d > 0) {
						echo $interval->d.' day'.($interval->d == 1 ? '' : 's');
					}elseif($interval->h > 0) {
						echo $interval->h.' hour'.($interval->h == 1 ? '' : 's');
					}elseif($interval->i > 0) {
						echo $interval->i.' minute'.($interval->i == 1 ? '' : 's');
					}elseif($interval->s > 0) {
						echo $interval->s.' second'.($interval->s == 1 ? '' : 's');
					}else{
						echo '1 second';
					}
					echo '.';
					if($user->dreamt_gold > 0 || $user->dreamt_rune_shards > 0 || $user->dreamt_gems > 0) echo ' <strong>They have found';
					if($user->dreamt_gold > 0) echo ' '.$user->dreamt_gold.' gold';
					if($user->dreamt_rune_shards > 0) {
						if($user->dreamt_gold > 0) {
							if($user->dreamt_gems == 0) {
								echo ' and';
							}else{
								echo ',';
							}
						}
						echo ' '.$user->dreamt_rune_shards.' rune shard'.($user->dreamt_rune_shards  == 1 ? '' : 's');
					}
					if($user->dreamt_gems > 0) {
						if($user->dreamt_gold > 0 || $user->dreamt_rune_shards > 0) {
							if($user->dreamt_gold > 0 && $user->dreamt_rune_shards > 0) {
								echo ', and';
							}else{
								echo ' and';
							}
						}
						echo ' '.$user->dreamt_gems.' gem'.($user->dreamt_gems  == 1 ? '' : 's');
					}
					if($user->dreamt_gold > 0 || $user->dreamt_rune_shards > 0 || $user->dreamt_gems > 0) echo '!</strong>'; ?>
		<br>You can't start new Gauntlet Runs while in Dream Mode.
		<br>
		<?= $this->Html->link('Exit Dream Mode', ['controller' => 'users', 'action' => 'exit-dream-mode'], ['class'=>'btn btn-success']); ?>					
	</div>
	<?php }*/ ?>
    <?php if(count($monsters_in_gauntlet_run) > 0) { ?>
        <h4>Monsters running the Gauntlet</h3>
    <table  class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            foreach ($monsters_in_gauntlet_run as $monster): ?>
            <tr>
                <td><?= $monster->name; ?></td>
                <td>
                    <?php
                        if((int)$monster->in_gauntlet_run_until->toUnixString() <= time()) {
                            echo $this->Html->link(__('View Results'), ['controller' => 'gauntlet_runs', 'action' => 'complete-run', $monster->id], ['class' => 'btn btn-success']);
                        }else{
                            $now = new DateTime();
                            $future_date = $monster->in_gauntlet_run_until;
                            
                            $interval = $future_date->diff($now);
                            echo 'Completes the Gauntlet in ';
                            if($interval->h > 0) {
                                echo $interval->h.' hour'.($interval->h == 1 ? '' : 's');
                            }elseif($interval->i > 0) {
                                echo $interval->i.' minute'.($interval->i == 1 ? '' : 's');
                            }elseif($interval->s > 0) {
                                echo $interval->s.' second'.($interval->s == 1 ? '' : 's');
                            }else{
                                echo '1 second';
                            }
                            echo '.';
                        }
                    ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
	</table>
    <?php } ?>
    <?php if(count($gauntlet_runs) > 0) { ?>
        <h4>Completed Gauntlet Runs</h3>
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
            <td class="actions" style="text-align:right;">
                <?php echo $this->Html->link(__('Watch Battles'), ['controller' => 'gauntlet_runs', 'action' => 'view_battles', $gauntlet_run->id, 'admin' => false], ['class' => 'btn btn-primary']); ?>
                <?php echo $this->Html->link(__('View Results'), ['controller' => 'gauntlet_runs', 'action' => 'view_results', $gauntlet_run->id, 'admin' => false], ['class' => 'btn btn-primary']); ?>
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
    <?php } ?>
</div>