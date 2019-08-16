<?php
App::uses('AppModel', 'Model');
/**
 * Skill Model
 *
 * @property Type $Type
 * @property SkillEffect $SkillEffect
 */
class Rune extends AppModel {



	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Type' => array(
			'className' => 'Type',
			'foreignKey' => 'type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Monster' => array(
			'className' => 'Monster',
			'foreignKey' => 'in_use_by_monster_id'
		)
	);
	
/*
 * static upgrades: Model::function()
 * @access static
 */
	 public static function upgrades() {
		$upgrades =  [
			'unlock_type' => 'Unlock Type',
			'damage_level' => 'Damage Increase',
			'healing_level' => 'Healing Increase',
			'critical_chance_level' => 'Critical Chance Increase',
			'cast_again_level' => 'Overload Chance Increase',
			'casting_speed_level' => 'Casting Speed Increase',
			'health_level' => 'Monster Health Increase'
		];
		return $upgrades;
	}
		
	
}
