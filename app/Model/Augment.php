<?php
App::uses('AppModel', 'Model');
/**
 * Augment Model
 *
 */
class Augment extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';
	
	/*
 * static effects: Model::function()
 * @access static
 */
	 public static function effects() {
		$types = [
			'Type',
			'Cast Speed',
			'Damage Increase',
			'Healing Increase',
			'Healing Over Time Duration Increase',
			'Damage Over Time Duration Increase',
			'Chance To Cast Again'
		];
		$type_options = array_combine($types, $types);
		return $type_options;
	}

}
