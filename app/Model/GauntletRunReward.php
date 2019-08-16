<?php
App::uses('AppModel', 'Model');


class GauntletRunReward extends AppModel {




	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = [
		'GauntletRun' => [
			'className' => 'GauntletRun',
			'foreignKey' => 'gauntlet_run_id'
		],
		'Skill' => [
			'className' => 'Skill',
			'foreignKey' => 'skill_id'
		],
		'Ultimate' => [
			'className' => 'Ultimate',
			'foreignKey' => 'ultimate_id'
		]
	];

}
