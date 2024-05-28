<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\Query\SelectQuery;

class MonstersTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('Timestamp');
		$this->belongsTo('Users');
		$this->belongsTo('Types');
		$this->belongsTo('SecondaryTypes')
			->setForeignKey('secondary_type_id')
			->setClassName('Types');
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
    
    public function createMonsterForUser($user_id, $type_id, $secondary_type_id = null, $elo_rating = 2100) {

		$monster = $this->newEntity(
			[
				'user_id' => $user_id,
				'new' => 1,
				'name' => 'New Monster',
				'type_id' => $type_id,
				'secondary_type_id' => $secondary_type_id,
				'strength' => 1,
				'agility' => 1,
				'dexterity' => 1,
				'intelligence' => 1,
				'luck' => 1,
				'vitality' => 1,
				'elo_rating' => $elo_rating
			],
			['validate' => false]
		);
		$this->save($monster);
		return $monster->id;
	}
	public function findForBattle(SelectQuery $query)
    {
        return $query->contain([
            'Types',
            'Rune1',
            'Rune2',
            'Rune3',
            'Skill1' => [
                'SkillEffects' => [
                    'SecondarySkillEffects',
                ],
                'Types'
            ],
            'Skill2' => [
                'SkillEffects' => [
                    'SecondarySkillEffects'
                ],
                'Types'
            ],
            'Skill3' => [
                'SkillEffects' => [
                    'SecondarySkillEffects'
                ],
                'Types'
            ],
            'Skill4' => [
                'SkillEffects' => [
                    'SecondarySkillEffects'
                ],
                'Types'
            ],
            'Ultimates' => [
                'SkillEffects' => [
                    'SecondarySkillEffects'
                ],
                'Types'
            ]
        ]);
    }
}