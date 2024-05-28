<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;

class SkillsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('Timestamp');
		$this->belongsTo('Types');
		$this->hasMany('Monster1')
            ->setClassName('Monsters')
            ->setForeignKey('skill_1_id');
		$this->hasMany('Monster2')
            ->setClassName('Monsters')
            ->setForeignKey('skill_2_id');
		$this->hasMany('Monster3')
            ->setClassName('Monsters')
            ->setForeignKey('skill_3_id');
		$this->hasMany('Monster4')
            ->setClassName('Monsters')
            ->setForeignKey('skill_4_id');
		$this->hasMany('UserSkills');
		$this->hasMany('SkillEffects');
    }
}