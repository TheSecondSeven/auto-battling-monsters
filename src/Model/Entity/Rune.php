<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Rune extends Entity
{
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