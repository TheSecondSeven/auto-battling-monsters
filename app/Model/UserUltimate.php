<?php
App::uses('AppModel', 'Model');
/**
 * Skill Model
 *
 * @property Type $Type
 * @property SkillEffect $SkillEffect
 */
class UserUltimate extends AppModel {



	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Ultimate' => array(
			'className' => 'Ultimate',
			'foreignKey' => 'ultimate_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
}