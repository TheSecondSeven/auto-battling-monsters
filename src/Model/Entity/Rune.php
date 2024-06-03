<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Rune extends Entity
{
    public static function upgrades($type) {
		$upgrades = [];
		if($type != 'Neutral') {
			$upgrades =  [
				'unlock_type' => 'Unlock '.$type.' Skills',
			];
		}
		$upgrades =  $upgrades + [
			'damage_level' => 'Increase '.$type.' Damage by '.RUNE_DAMAGE_INCREASE.'%',
			'healing_level' => 'Increase '.$type.' Healing by '.RUNE_HEALING_INCREASE.'%',
			'critical_chance_level' => 'Increase '.$type.' Critical Chance by '.RUNE_CRITICAL_CHANCE_INCREASE.'%',
			'cast_again_level' => 'Increase '.$type.' Overload Chance by '.RUNE_CAST_AGAIN_INCREASE.'%',
			'casting_speed_level' => 'Increase '.$type.' Casting Speed by '.RUNE_CASTING_SPEED_INCREASE.'%',
			'health_level' => 'Increase Monster Health by '.RUNE_HEALTH_INCREASE.'%'
		];
		return $upgrades;
	}
}