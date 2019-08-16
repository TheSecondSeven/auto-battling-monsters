<?php
App::uses('AppModel', 'Model');

class GauntletRunBattle extends AppModel {

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Opponent' => array(
			'className' => 'Monster',
			'foreignKey' => 'opponent_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
}
