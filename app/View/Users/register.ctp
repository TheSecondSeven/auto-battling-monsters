<div class="users form">
<?php echo $this->Form->create('User'); ?>
	<fieldset>
		<legend><?php echo __('Register'); ?></legend>
	<?php
		echo $this->Form->input('email');
		echo $this->Form->input('username');
		echo $this->Form->input('pwd', ['label' => 'Password', 'type' => 'password']);
		echo $this->Form->input('pwd_confirm', ['label' => 'Confirm Password', 'type' => 'password']);
		echo $this->Form->input('favorite_dessert', ['label' => 'Which of these desserts is your favorite?','options' => [
			'' => 'Select Your Favorite Dessert',
			'Ice Cream' => 'Ice Cream',
			'Crepe' => 'Crepe',
			'Jello' => 'Jello',
			'Strawberry Pie' => 'Strawberry Pie',
			'Chocolate Cake' => 'Chocolate Cake',
			'S\'more' => 'S\'more',
			'Cookie' => 'Cookie',
			'Banana Pudding' => 'Banana Pudding'
		]]);
		echo $this->Form->input('favorite_power_ranger', ['label' => 'Who is your favorite Power Ranger?','options' => [
			'' => 'Select Your Favorite Power Ranger',
			'Red' => 'Red',
			'Black' => 'Black',
			'Yellow' => 'Yellow',
			'Pink' => 'Pink',
			'Blue' => 'Blue',
			'Green' => 'Green',
			'White' => 'White'
		]]);
		echo $this->Form->input('favorite_nineties_cartoon', ['label' => 'Which of these 90\'s cartoons is your favorite?','options' => [
			'' => 'Select Your 90\'s Cartoon',
			'Doug' => 'Doug',
			'Animaniacs' => 'Animaniacs',
			'The Simpsons' => 'The Simpsons',
			'Beavis and Butt-Head' => 'Beavis and Butt-Head',
			'Rugrats' => 'Rugrats',
			'Pinky and the Brain' => 'Pinky and the Brain',
			'The Magic School Bus' => 'The Magic School Bus',
			'Gargoyles' => 'Gargoyles',
			'Daria' => 'Daria',
			'Hey Arnold!' => 'Hey Arnold!',
			'Darkwing Duck' => 'Darkwing Duck',
			'Rocko\'s Modern Life' => 'Rocko\'s Modern Life',
			'The Ren & Stimpy Show' => 'The Ren & Stimpy Show'
		]]);
		echo $this->Form->input('favorite_pizza_topping', ['label' => 'Which of these pizza toppings is your favorite?','options' => [
			'' => 'Select Your Favorite Pizza Topping',
			'Extra Cheese' => 'Extra Cheese',
			'Pepperoni' => 'Pepperoni',
			'Olives' => 'Olives',
			'Bell Peppers' => 'Bell Peppers',
			'Sausage' => 'Sausage',
			'Ham' => 'Ham',
			'Pineapple' => 'Pineapple',
			'Jalapeños' => 'Jalapeños',
			'Mushroom' => 'Mushroom',
			'Bacon' => 'Bacon',
			'Onions' => 'Onions'
		]]);
		echo $this->Form->input('favorite_day_of_the_week', ['label' => 'Which is your favorite day of the week?','options' => [
			'' => 'Select Your Favorite Day of the Week',
			'Monday' => 'Monday',
			'Tuesday' => 'Tuesday',
			'Wednesday' => 'Wednesday',
			'Thursday' => 'Thursday',
			'Friday' => 'Friday',
			'Saturday' => 'Saturday',
			'Sunday' => 'Sunday'
		]]);
		
		echo $this->Form->input('favorite_rpg_class', ['label' => 'Which of these is your favorite RPG Class?','options' => [
			'' => 'Select Your Favorite RPG Class',
			'Barbarian' => 'Barbarian',
			'Bard' => 'Bard',
			'Cleric' => 'Cleric',
			'Druid' => 'Druid',
			'Fighter' => 'Fighter',
			'Monk' => 'Monk',
			'Paladin' => 'Paladin',
			'Ranger' => 'Ranger',
			'Rogue' => 'Rogue',
			'Sorcerer' => 'Sorcerer',
			'Warlock' => 'Warlock',
			'Wizard' => 'Wizard'
		]]);
	?>
	</fieldset>
<?php echo $this->Form->end(__('Register')); ?>
</div>
