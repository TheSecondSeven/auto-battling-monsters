<?php
App::uses('AppModel', 'Model');
/**
 * SkillEffect Model
 *
 * @property Skill $Skill
 * @property SkillEffect $SecondarySkillEffect
 * @property SkillEffect $SkillEffect
 */
class SkillEffect extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'effect';


	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Skill' => array(
			'className' => 'Skill',
			'foreignKey' => 'skill_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Ultimate' => array(
			'className' => 'Ultimate',
			'foreignKey' => 'ultimate_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'PrimarySkillEffect' => array(
			'className' => 'SkillEffect',
			'foreignKey' => 'skill_effect_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'SecondarySkillEffect' => array(
			'className' => 'SkillEffect',
			'foreignKey' => 'skill_effect_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);
	
/*
 * static effects: Model::function()
 * @access static
 */
	 public static function effects() {
		$effects =  [
			'Physical Damage',
			'Magical Damage',
			'True Damage',
			'Leech',
			'Heal',
			'Heal Over Time',
			//'Heal On Hit',
			'Attack Up',
			'Attack Down',
			'Defense Up',
			'Defense Down',
			'Speed Up',
			'Speed Down',
			'Evade Up',
			'Evade Down',
			'Bubble',
			'Stun',
			'Sleep',
			'Burn',
			'Wet',
			'Freeze',
			'Poison',
			'Infect',
			'Confuse',
			'Cleanse',
			'Purge',
			'Consume',
			'Undying',
			'Random Amount',
			//'Delay'
		];
		$effect_options = array_combine($effects, $effects);
		return $effect_options;
	}

/*
 * static targets: Model::function()
 * @access static
 */
	 public static function targets() {
		$targets =  [
			'Single Enemy',
			'Self',
			'All Enemies',
			'Everyone',
			'All Allies',
			//'Environment'
		];
		$target_options = array_combine($targets, $targets);
		return $target_options;
	}
	

}
