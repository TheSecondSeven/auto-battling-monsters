<?php $this->extend('../layout/dashboard'); ?>
<div class="campaign index">
    <h2>Available Quests</h2>
    <?php if(count($available_quests) > 0) { ?>
	<table class="table table-striped">
        <thead>
            <tr>
                    <th>Quest</th>
                    <th>Required Rest</th>
                    <th>Enemies</th>
                    <th>Rewards</th>
                    <th class="actions">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($available_quests as $quest): ?>
            <tr>
                <td><?= $quest->title ?></td>
                <td><?php if(empty($quest->required_rest)) { echo 'None'; }else{
                    $days = floor($quest->required_rest / (60 * 24));
                    $hours = floor(($quest->required_rest % (60 * 24)) / 60);
                    $minutes = floor($quest->required_rest % 60);
					if($days > 0) {
						echo $days.' day'.($days == 1 ? '' : 's');
					}elseif($hours > 0) {
						echo ' '.$hours.' hour'.($hours == 1 ? '' : 's');
					}elseif($minutes > 0) {
						echo ' '.$minutes.' minute'.($hours == 1 ? '' : 's');
					}
                } ?></td>
                <td><?= $quest->get('monsters') ?></td>
                <td><?= $quest->get('rewards') ?></td>
                <td><?= $this->Html->link('View', ['controller' => 'quests', 'action' => 'view', $quest->id], ['class' => 'btn btn-primary']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
	</table>
    <?php }else{ ?>
    <h5>No campgaign quests available at this time. Please check back soon!</h5>
    <?php } ?>
    <?php if(count($completed_quests) > 0) { ?>
    <h3>Completed Quests</h3>
	<table class="table table-striped">
        <thead>
            <tr>
                    <th>Quest</th>
                    <th>Completed By</th>
                    <th>Rewards Earned</th>
                    <th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($completed_quests as $quest): ?>
            <tr>
                <td><?= $quest->title ?></td>
                <td><?= (!empty($quest->monster->id) ? $quest->monster->name.' on ' : 'On ').$quest->_matchingData['QuestsUsers']->completed->format('F jS, Y'); ?></td>
                <td><?= $quest->get('user_rewards') ?></td>
                <td><?= $this->Html->link('Watch Battle', ['controller' => 'quests', 'action' => 'battle', $quest->id], ['class' => 'btn btn-success']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
	</table>
    <?php } ?>
</div>

