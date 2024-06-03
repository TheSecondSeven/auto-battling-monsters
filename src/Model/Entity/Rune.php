<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Rune extends Entity
{
    public static function upgrades($type) {
		$upgrades =  [
			'unlock_type' => 'Unlocks '.$type.' Skills',
			'damage_level' => $type.' Damage Increase',
			'healing_level' => $type.' Healing Increase',
			'critical_chance_level' => $type.' Critical Chance Increase',
			'cast_again_level' => $type.' Overload Chance Increase',
			'casting_speed_level' => $type.' Casting Speed Increase',
			'health_level' => 'Monster Health Increase'
		];
		return $upgrades;
	}
}