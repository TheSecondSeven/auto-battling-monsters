<?php 
use BootstrapUI\View\Helper\FormHelper;
$this->extend('../layout/TwitterBootstrap/signin'); ?>
<div class="users form">
    <h3>Register</h3>
    <?= $this->Form->create($user, [
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
        <?= $this->Form->control('username', ['required' => true]) ?>
        <?= $this->Form->control('email', ['required' => true]) ?>
        <?= $this->Form->control('password', ['required' => true]) ?>
        <?= $this->Form->control('confirm_password', ['type'=>'password','required' => true]) ?>
        <?= $this->Form->control('favorite_dessert', [
            'required' => true,
            'label' => 'Which of these desserts is your favorite?',
            'options' => [
                '' => 'Select Your Favorite Dessert',
                'Ice Cream' => 'Ice Cream',
                'Crepe' => 'Crepe',
                'Jello' => 'Jello',
                'Strawberry Pie' => 'Strawberry Pie',
                'Chocolate Cake' => 'Chocolate Cake',
                'S\'more' => 'S\'more',
                'Cookie' => 'Cookie',
                'Banana Pudding' => 'Banana Pudding'
            ]
        ]) ?>
        <?= $this->Form->control('favorite_power_ranger', [
            'required' => true,
            'label' => 'Who is your favorite Power Ranger?',
            'options' => [
                '' => 'Select Your Favorite Power Ranger',
                'Red' => 'Red',
                'Black' => 'Black',
                'Yellow' => 'Yellow',
                'Pink' => 'Pink',
                'Blue' => 'Blue',
                'Green' => 'Green',
                'White' => 'White'
            ]
        ]) ?>
        <?= $this->Form->control('favorite_nineties_cartoon', [
            'required' => true,
            'label' => 'Which of these 90\'s cartoons is your favorite?',
            'options' => [
                '' => 'Select Your 90\'s Cartoon',
                'Doug' => 'Doug',
                'X-Men' => 'X-Men',
                'Spider-man' => 'Spider-man',
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
            ]
        ]) ?>
        <?= $this->Form->control('favorite_pizza_topping', [
            'required' => true,
            'label' => 'Which of these pizza toppings is your favorite?',
            'options' => [
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
            ]
        ]) ?>
        <?= $this->Form->control('favorite_day_of_the_week', [
            'required' => true,
            'label' => 'Which is your favorite day of the week?',
            'options' => [
                '' => 'Select Your Favorite Day of the Week',
                'Monday' => 'Monday',
                'Tuesday' => 'Tuesday',
                'Wednesday' => 'Wednesday',
                'Thursday' => 'Thursday',
                'Friday' => 'Friday',
                'Saturday' => 'Saturday',
                'Sunday' => 'Sunday'
            ]
        ]) ?>
        <?= $this->Form->control('favorite_rpg_class', [
            'required' => true,
            'label' => 'Which of these is your favorite RPG Class?',
            'options' => [
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
            ]
        ]) ?>
    <?= $this->Form->submit(__('Register')); ?>
    <?= $this->Form->end() ?>
</div>