<?php
App::uses('AppModel', 'Model');
/**
 * SkillEffect Model
 *
 * @property Skill $Skill
 * @property SkillEffect $SecondarySkillEffect
 * @property SkillEffect $SkillEffect
 */
class GauntletRun extends AppModel {



	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Monster' => array(
			'className' => 'Monster',
			'foreignKey' => 'monster_id'
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id'
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

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'GauntletRunBattle' => array(
			'className' => 'GauntletRunBattle',
			'foreignKey' => 'gauntlet_run_id',
			'order' => 'GauntletRunBattle.order ASC'
		),
		'GauntletRunReward' => array(
			'className' => 'GauntletRunReward',
			'foreignKey' => 'gauntlet_run_id',
			'order' => [
				'FIELD(GauntletRunReward.rarity,"Common","Uncommon","Rare","Epic","Legendary")'
			]
		)
		/*
		'AvailableReward' => array(
			'className' => 'AvailableReward',
			'foreignKey' => 'gauntlet_run_id',
			'conditions' => [
				'AvailableReward.chosen' => 0
			],
			'order' => [
				'FIELD(AvailableReward.rarity,"Common","Uncommon","Rare","Epic","Legendary")'
			]
		)*/
	);
	
}
