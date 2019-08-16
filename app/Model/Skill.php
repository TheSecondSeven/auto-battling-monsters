<?php
App::uses('AppModel', 'Model');
/**
 * Skill Model
 *
 * @property Type $Type
 * @property SkillEffect $SkillEffect
 */
class Skill extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';


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
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'SkillEffect' => array(
			'className' => 'SkillEffect',
			'foreignKey' => 'skill_id',
			'dependent' => false,
			'conditions' => [
				'SkillEffect.skill_effect_id' => 0
			],
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

}
