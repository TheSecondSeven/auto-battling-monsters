<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\Query\SelectQuery;

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
      public function rarities() {
		return [
                  'Common' => 'Common',
                  'Uncommon' => 'Uncommon',
                  'Rare' => 'Rare',
                  'Epic' => 'Epic',
                  'Legendary' => 'Legendary'
            ];
	}

      public function rarityLevels() {
		return [
                  'Common' => 0,
                  'Uncommon' => 1,
                  'Rare' => 2,
                  'Epic' => 3,
                  'Legendary' => 4
            ];
	}

	public function findOrderedByNew(SelectQuery $query)
      {
            return $query->contain(['UserSkills'])->order(['UserSkills.new' => 'DESC']);
      }
}