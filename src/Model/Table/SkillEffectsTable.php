<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;

class SkillEffectsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('Timestamp');
		$this->belongsTo('Skills');
		$this->belongsTo('Ultimates');
		$this->belongsTo('PrimarySkillEffects')
            ->setClassName('SkillEffects');
		$this->hasMany('SecondarySkillEffects')
            ->setClassName('SkillEffects');
    }
    public static function effects() {
		$effects =  [
			'Physical Damage',
			'Magical Damage',
			'True Damage',
			'Leech',
			'Heal',
			'Heal Over Time',
			//'Heal On Hit',
			'Attack Up',
			'Attack Down',
			'Defense Up',
			'Defense Down',
			'Speed Up',
			'Speed Down',
			'Evade Up',
			'Evade Down',
			'Bubble',
			'Stun',
			'Sleep',
			'Burn',
			'Wet',
			'Freeze',
			'Poison',
			'Infect',
			'Confuse',
			'Cleanse',
			'Purge',
			'Consume',
			'Undying',
			'Random Amount',
			'Blind',
			'Reflect'
			//'Delay'
		];
		$effect_options = array_combine($effects, $effects);
		return $effect_options;
	}

	public static function targets() {
		$targets =  [
			'Single Enemy',
			'Self',
			'All Enemies',
			'Everyone',
			'All Allies',
			'Same as Primary Effect'
			//'Environment'
		];
		$target_options = array_combine($targets, $targets);
		return $target_options;
	}
}