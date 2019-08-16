<?php
App::uses('AppModel', 'Model');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
/**
 * User Model
 *
 * @property Monster $Monster
 */
class User extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'username';
	public $validate = array(
        'username' => [
        	'alphaNumeric',
		    'unique' => [
		        'rule' => 'isUnique',
		        'message'   => 'This username is already taken.',
		        'required' => 'create'
		    ]
		],
        'email' => [
        	'email',
		    'unique' => [
		        'rule' => 'isUnique',
		        'message'   => 'There is already an account tied to this email address.',
		        'required' => 'create'
		    ]
		],
        'pwd' => array(
		    'length' => array(
		        'rule'      => array('between', 6, 40),
		        'message'   => 'Your password must be between 6 and 40 characters.',
		    ),
		),
		'pwd_confirm' => array(
		    'length' => array(
		        'rule'      => array('between', 6, 40),
		        'message'   => 'Your password must be between 6 and 40 characters.',
		    ),
		    'compare'    => array(
		        'rule'      => array('validate_passwords'),
		        'message' => 'The passwords you entered do not match.',
		    )
		),
        'favorite_dessert' => [
        	'rule' => 'notBlank',
			'message' => 'Please select a favorite dessert.',
			'on' => 'create'
		],
        'favorite_power_ranger' => [
        	'rule' => 'notBlank',
			'message' => 'Please select a favorite Power Ranger.',
			'on' => 'create'
		],
        'favorite_nineties_cartoon' => [
        	'rule' => 'notBlank',
			'message' => 'Please select a favorite 90\'s cartoon.',
			'on' => 'create'
		],
        'favorite_pizza_topping' => [
        	'rule' => 'notBlank',
			'message' => 'Please select a favorite pizza topping.',
			'on' => 'create'
		],
        'favorite_day_of_the_week' => [
        	'rule' => 'notBlank',
			'message' => 'Please select a favorite day of the week.',
			'on' => 'create'
		],
        'favorite_rpg_class' => [
        	'rule' => 'notBlank',
			'message' => 'Please select a favorite RPG Class.',
			'on' => 'create'
		]
    );

	public $answer_values = [
		'Ice Cream' => [
			'Water'
		],
		'Crepe' => [
			'Electric'
		],
		'Jello' => [
			'Earth',
			'Poison'
		],
		'Strawberry Pie' => [
			'Fire',
			'Earth'
		],
		'Chocolate Cake' => [
			'Undead'
		],
		'S\'more' => [
			'Fire',
			'Fighting',
			'Undead'
		],
		'Cookie' => [
			'Fire'
		],
		'Banana Pudding' => [
			'Earth',
			'Electric'
		],
		'Red' => [
			'Fire'
		],
		'Black' => [
			'Undead'
		],
		'Yellow' => [
			'Electric'
		],
		'Pink' => [
			'Fighting'
		],
		'Blue' => [
			'Water'
		],
		'Green' => [
			'Earth'
		],
		'White' => [
			'Poison'
		],
		'Doug' => [
			'Earth'
		],
		'Animaniacs' => [
			'Electric'
		],
		'The Simpsons' => [
			'Undead'
		],
		'Beavis and Butt-Head' => [
			'Fighting'
		],
		'Rugrats' => [
			'Water'
		],
		'Pinky and the Brain' => [
			'Poison'
		],
		'The Magic School Bus' => [
			'Electric'
		],
		'Gargoyles' => [
			'Undead'
		],
		'Daria' => [
			'Undead'
		],
		'Hey Arnold!' => [
			'Fire'
		],
		'Darkwing Duck' => [
			'Poison',
			'Fighting',
			'Undead'
		],
		'Rocko\'s Modern Life' => [
			'Fire',
			'Earth',
			'Electric',
		],
		'The Ren & Stimpy Show' => [
			'Fire',
			'Electric',
			'Poison',
			'Fighting'
		],
		'Extra Cheese' => [
			'Water'
		],
		'Pepperoni' => [
			'Fire',
			'Fighting'
		],
		'Olives' => [
			'Water',
			'Undead'
		],
		'Bell Peppers' => [
			'Water',
			'Earth'
		],
		'Sausage' => [
			'Earth',
			'Undead'
		],
		'Ham' => [
			'Fighting'
		],
		'Pineapple' => [
			'Electric'
		],
		'Jalapeños' => [
			'Fire',
			'Earth'
		],
		'Mushroom' => [
			'Earth',
			'Poison'
		],
		'Bacon' => [
			'Undead'
		],
		'Onions' => [
			'Water'
		],
		'Monday' => [
			'Fire'
		],
		'Tuesday' => [
			'Water'
		],
		'Wednesday' => [
			'Earth'
		],
		'Thursday' => [
			'Electric'
		],
		'Friday' => [
			'Poison'
		],
		'Saturday' => [
			'Fighting'
		],
		'Sunday' => [
			'Undead'
		],
		'Barbarian' => [
			'Fire',
			'Fighting'
		],
		'Bard' => [
			'Electric',
			'Poison',
			'Fighting'
		],
		'Cleric' => [
			'Fire',
			'Water'
		],
		'Druid' => [
			'Earth'
		],
		'Fighter' => [
			'Fighting'
		],
		'Monk' => [
			'Fire',
			'Water',
			'Earth',
			'Fighting'
		],
		'Paladin' => [
			'Fire',
			'Fighting',
			'Undead'
		],
		'Ranger' => [
			'Water',
			'Earth',
			'Poison',
			'Fighting'
		],
		'Rogue' => [
			'Poison',
			'Fighting'
		],
		'Sorcerer' => [
			'Fire',
			'Water',
			'Earth',
			'Electric',
			'Undead'
		],
		'Warlock' => [
			'Fire',
			'Poison',
			'Undead'
		],
		'Wizard' => [
			'Fire',
			'Water',
			'Earth',
			'Electric'
		]
	];
	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Monster' => array(
			'className' => 'Monster',
			'foreignKey' => 'user_id'
		)
	);
	
	public function validate_passwords() {
    	return $this->data[$this->alias]['pwd'] === $this->data[$this->alias]['pwd_confirm'];
	}
	
	public function beforeSave($options = array()) {
		
	    if(isset($this->data[$this->alias]['password'])){
            $passwordHasher = new SimplePasswordHasher(array('hashType' => 'sha256'));
	        $this->data[$this->alias]['password'] = $passwordHasher->hash(
	            $this->data[$this->alias]['password']
	        );
	    }
	    return true;
	}
}
