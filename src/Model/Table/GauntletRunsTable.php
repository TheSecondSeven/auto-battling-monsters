<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;

class GauntletRunsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('Timestamp');
		$this->belongsTo('Users');
		$this->belongsTo('Monsters');
		$this->hasMany('GauntletRunBattles');
		$this->hasMany('GauntletRunRewards');
        $this->belongsTo('Rune1')
			->setForeignKey('rune_1_id')
			->setClassName('Runes');
		$this->belongsTo('Rune2')
			->setForeignKey('rune_2_id')
			->setClassName('Runes');
		$this->belongsTo('Rune3')
			->setForeignKey('rune_3_id')
			->setClassName('Runes');
		$this->belongsTo('Skill1')
			->setForeignKey('skill_1_id')
			->setClassName('Skills');
		$this->belongsTo('Skill2')
			->setForeignKey('skill_2_id')
			->setClassName('Skills');
		$this->belongsTo('Skill3')
			->setForeignKey('skill_3_id')
			->setClassName('Skills');
		$this->belongsTo('Skill4')
			->setForeignKey('skill_4_id')
			->setClassName('Skills');
		$this->belongsTo('Ultimates');
    }
}