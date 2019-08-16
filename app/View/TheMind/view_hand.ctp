<?php 
$round = 0;
if(!empty($played_numbers))	{
echo '<h2>Played Numbers</h2>';
foreach($played_numbers as $number) {
	if($number['TheMindNumber']['user_id'] == $user_id) 
		$round++;
	echo $number['TheMindNumber']['number'].' | '.$number['User']['username'].': ';
	if($number['TheMindNumber']['played']) echo 'Played';
	if($number['TheMindNumber']['skipped']) echo 'Skipped';
	if($number['TheMindNumber']['destroyed']) echo 'Destroyed';
	echo '<br>';
}
}
echo '<h2>Your Hand</h2>';
foreach($numbers_in_hand as $index=>$number) {
	if($number['TheMindNumber']['user_id'] == $user_id) 
		$round++;
	echo $number['TheMindNumber']['number'];
	if($index == 0) echo ' - '.$this->Html->link(__('Play '.$number['TheMindNumber']['number']), array('action' => 'play_number', $number['TheMindNumber']['number']));
	echo '<br>';
}
if(empty($numbers_in_hand)) {
	echo 'You have no more numbers.';
	
	echo '<br><br>'.$this->Html->link(__('Start Next Round'), array('action' => 'start_round', $round + 1));
	echo '<br><br>'.$this->Html->link(__('Join Next Round'), array('action' => 'join_round'));
}else{
	echo '<br><br>'.$this->Html->link(__('Refresh Hand'), array('action' => 'view_hand'));
}
echo '<br><br>'.$this->Html->link(__('Destroy the Lowest Number for Each Player'), array('action' => 'destroy_numbers'));