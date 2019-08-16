<?php
App::uses('AppModel', 'Model');
/**
 * Monster Model
 *
 * @property User $User
 * @property Type $Type
 * @property Skill $Skill1
 * @property Skill $Skill2
 * @property Skill $Skill3
 * @property Skill $Skill4
 */
class Monster extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';
	public $validate = [
        'rune_1_id' => [
		    'compare2'    => [
		        'rule'      => array('validate_unique_runes', 1, 2),
		        'message' => 'You cannot use the same rune more than once.',
		    ],
		    'compare3'    => [
		        'rule'      => array('validate_unique_runes', 1, 3),
		        'message' => 'You cannot use the same rune more than once.',
		    ],
		],
        'rune_2_id' => [
		    'compare1'    => [
		        'rule'      => array('validate_unique_runes', 2, 1),
		        'message' => 'You cannot use the same rune more than once.',
		    ],
		    'compare3'    => [
		        'rule'      => array('validate_unique_runes', 2, 3),
		        'message' => 'You cannot use the same rune more than once.',
		    ],
		],
        'rune_3_id' => [
		    'compare1'    => [
		        'rule'      => array('validate_unique_runes', 3, 1),
		        'message' => 'You cannot use the same rune more than once.',
		    ],
		    'compare2'    => [
		        'rule'      => array('validate_unique_runes', 3, 2),
		        'message' => 'You cannot use the same rune more than once.',
		    ],
		],
        'skill_1_id' => [
		    'compare2'    => [
		        'rule'      => array('validate_unique_skills', 1, 2),
		        'message' => 'You cannot use the same skill more than once.',
		    ],
		    'compare3'    => [
		        'rule'      => array('validate_unique_skills', 1, 3),
		        'message' => 'You cannot use the same skill more than once.',
		    ],
		    'compare4'    => [
		        'rule'      => array('validate_unique_skills', 1, 4),
		        'message' => 'You cannot use the same skill more than once.',
		    ]
		],
        'skill_2_id' => [
		    'compare1'    => [
		        'rule'      => array('validate_unique_skills', 2, 1),
		        'message' => 'You cannot use the same skill more than once.',
		    ],
		    'compare3'    => [
		        'rule'      => array('validate_unique_skills', 2, 3),
		        'message' => 'You cannot use the same skill more than once.',
		    ],
		    'compare4'    => [
		        'rule'      => array('validate_unique_skills', 2, 4),
		        'message' => 'You cannot use the same skill more than once.',
		    ]
		],
        'skill_3_id' => [
		    'compare1'    => [
		        'rule'      => array('validate_unique_skills', 3, 1),
		        'message' => 'You cannot use the same skill more than once.',
		    ],
		    'compare2'    => [
		        'rule'      => array('validate_unique_skills', 3, 2),
		        'message' => 'You cannot use the same skill more than once.',
		    ],
		    'compare4'    => [
		        'rule'      => array('validate_unique_skills', 3, 4),
		        'message' => 'You cannot use the same skill more than once.',
		    ]
		],
        'skill_4_id' => [
		    'compare1'    => [
		        'rule'      => array('validate_unique_skills', 4, 1),
		        'message' => 'You cannot use the same skill more than once.',
		    ],
		    'compare2'    => [
		        'rule'      => array('validate_unique_skills', 4, 2),
		        'message' => 'You cannot use the same skill more than once.',
		    ],
		    'compare3'    => [
		        'rule'      => array('validate_unique_skills', 4, 3),
		        'message' => 'You cannot use the same skill more than once.',
		    ]
		]
	];

	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id'
		),
		'Type' => array(
			'className' => 'Type',
			'foreignKey' => 'type_id'
		),
		'SecondaryType' => array(
			'className' => 'Type',
			'foreignKey' => 'secondary_type_id'
		),
		'Rune1' => array(
			'className' => 'Rune',
			'foreignKey' => 'rune_1_id'
		),
		'Rune2' => array(
			'className' => 'Rune',
			'foreignKey' => 'rune_2_id'
		),
		'Rune3' => array(
			'className' => 'Rune',
			'foreignKey' => 'rune_3_id'
		),
		'Skill1' => array(
			'className' => 'Skill',
			'foreignKey' => 'skill_1_id'
		),
		'Skill2' => array(
			'className' => 'Skill',
			'foreignKey' => 'skill_2_id'
		),
		'Skill3' => array(
			'className' => 'Skill',
			'foreignKey' => 'skill_3_id'
		),
		'Skill4' => array(
			'className' => 'Skill',
			'foreignKey' => 'skill_4_id'
		),
		'Ultimate' => array(
			'className' => 'Ultimate',
			'foreignKey' => 'ultimate_id'
		)
	);
	
	public function validate_unique_skills($check, $skill_number, $skill_number_2) {
    	return ($this->data[$this->alias]['skill_'.$skill_number.'_id'] == 0 || $this->data[$this->alias]['skill_'.$skill_number.'_id'] != $this->data[$this->alias]['skill_'.$skill_number_2.'_id']);
	}
	
	public function validate_unique_runes($check, $rune_number, $rune_number_2) {
    	return ($this->data[$this->alias]['rune_'.$rune_number.'_id'] == 0 || $this->data[$this->alias]['rune_'.$rune_number.'_id'] != $this->data[$this->alias]['rune_'.$rune_number_2.'_id']);
	}
	
	public function createMonster($user_id, $type_id, $secondary_type_id = 0, $elo_rating = 2100) {
		$this->create();
		$monster['Monster'] = [
			'id' => null,
			'user_id' => $user_id,
			'new' => 1,
			'name' => 'New Monster',
			'type_id' => $type_id,
			'secondary_type_id' => $secondary_type_id,
			'strength' => 1,
			'agility' => 1,
			'dexterity' => 1,
			'intelligence' => 1,
			'luck' => 1,
			'vitality' => 1,
			'elo_rating' => $elo_rating
		];
		$this->save($monster);
		return $this->id;
	}
	
}
