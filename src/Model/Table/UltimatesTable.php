<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;

class UltimatesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('Timestamp');
		$this->hasMany('UserUltimates');
		$this->hasMany('Monsters');
		$this->belongsTo('Types');
		$this->belongsTo('SecondaryTypes')
			->setForeignKey('secondary_type_id')
			->setClassName('Types');
		$this->hasMany('SkillEffects');
    }
}