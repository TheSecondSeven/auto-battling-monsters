<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Rune extends Entity
{
    public static function upgrades($type = null) {
		if(empty($type)) {

			$upgrades =  [
				'unlock_type' => 'Unlock Skills',
				'damage_level' => 'Increase Damage by '.RUNE_DAMAGE_INCREASE.'%',
				'healing_level' => 'Increase Healing by '.RUNE_HEALING_INCREASE.'%',
				'critical_chance_level' => 'Increase Critical Chance by '.RUNE_CRITICAL_CHANCE_INCREASE.'%',
				'cast_again_level' => 'Increase Overload Chance by '.RUNE_CAST_AGAIN_INCREASE.'%',
				'casting_speed_level' => 'Increase Casting Speed by '.RUNE_CASTING_SPEED_INCREASE.'%',
				'health_level' => 'Increase Monster Health by '.RUNE_HEALTH_INCREASE.'%'
			];
			return $upgrades;
		}
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
	protected function _getCurrentLevel()
    {
		$current_level = 0;
		if(!empty($this->unlock_type)) $current_level++;
		$current_level += $this->damage_level;
		$current_level += $this->healing_level;
		$current_level += $this->critical_chance_level;
		$current_level += $this->cast_again_level;
		$current_level += $this->health_level;
		$current_level += $this->casting_speed_level;
		return $current_level;
    }
}