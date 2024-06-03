<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Rune extends Entity
{
    public static function upgrades($type) {
		$upgrades =  [
			'unlock_type' => 'Unlock '.$type.' Skills',
			'damage_level' => 'Increase '.$type.' Damage',
			'healing_level' => 'Increase '.$type.' Healing',
			'critical_chance_level' => 'Increase '.$type.' Critical Chance',
			'cast_again_level' => 'Increase '.$type.' Overload Chance',
			'casting_speed_level' => 'Increase '.$type.' Casting Speed',
			'health_level' => 'Increase Monster Health'
		];
		return $upgrades;
	}
}