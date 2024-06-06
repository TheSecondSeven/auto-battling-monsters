<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
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
    
    public function createMonsterForUser($user_id, $type_id = null, $secondary_type_id = null, $elo_rating = 2100) {
		if(empty($type_id)) {
			$types = TableRegistry::getTableLocator()->get('Types')
				->find()
				->where([
					'Types.name != "Neutral"',
					'Types.name != "Flying"',
				])
				->all()
				->toList();
			
			//check if they already have one of every type
			$doesnt_have_types = TableRegistry::getTableLocator()->get('Types')
				->find()
				->where([
					'Types.name != "Neutral"',
					'Types.name != "Flying"'
				])
				->notMatching('Monsters', function ($q) use ($user_id) {
					return $q->where(['Monsters.user_id' => $user_id]);
				})
				->all()
				->toList();
			
			$monster_type = null;
			
			while($type_id == null) {
				if(!empty($doesnt_have_types)) {
					$type_id = $doesnt_have_types[rand(0,count($doesnt_have_types) - 1)]->id;
				}else{
					$type_id = $types[rand(0,count($types) - 1)]->id;
				}
			}
		}


		$monster = $this->newEntity(
			[
				'user_id' => $user_id,
				'new' => 1,
				'name' => 'New Monster',
				'type_id' => $type_id,
				'secondary_type_id' => $secondary_type_id,
				'strength' => 1,
				'dexterity' => 1,
				'intelligence' => 1,
				'luck' => 1,
				'vitality' => 1,
				'elo_rating' => $elo_rating
			],
			['validate' => false]
		);
		$this->save($monster);
		return $monster;
	}
	public function createDualTypeMonsterForUser($user_id, $type_id = null, $secondary_type_id = null, $elo_rating = 2100) {
		if(empty($type_id) || empty($secondary_type_id)) {
			$types = TableRegistry::getTableLocator()->get('Types')
				->find()
				->where([
					'Types.name != "Neutral"',
					'Types.name != "Flying"',
				])
				->all()
				->toList();
			
			//check if they have all combos
			$dual_monster_count = $this
				->find()
				->where([
					'Monsters.user_id' => $user_id,
					'Monsters.type_id != 0',
					'Monsters.secondary_type_id != 0'
				])
				->contain([])
				->count();
			$type_count = count($types);
			$has_all_combos = false;

			$combos = 1;
			if($type_count > 2) {
				$has_all_combos = false;
				$fact = $type_count;
				$ffact1 = 1;

				while($fact >= 1)
				{
					$ffact1 = $fact * $ffact1;
					$fact--;
				}
				$fact = $type_count - 2;
				$ffact2 = 1;
				while($fact >= 1)
				{
					$ffact2 = $fact * $ffact2;
					$fact--;
				}
				$combos = $ffact1 / (2 * $ffact2);
			}
			if($dual_monster_count >= $combos) {
				$has_all_combos = true;
			}
			
			$monster_type = null;
			$secondary_monster_type = null;
			while($monster_type == null) {
				$monster_type = $types[rand(0,count($types) - 1)];
				$secondary_monster_type = null;
				while($secondary_monster_type == null) {
					$secondary_monster_type = $types[rand(0,count($types) - 1)];
					if($monster_type->id == $secondary_monster_type->id) {
						$secondary_monster_type = null;
					}
				}
				if($has_all_combos == false) {
					//check if they have this monster
					$monster_check = $this
						->find()
						->where([
							'Monsters.user_id' => $user_id,
							'OR' => [
								0 => [
									'Monsters.type_id' => $monster_type->id,
									'Monsters.secondary_type_id' => $secondary_monster_type->id
								],
								1 => [
									'Monsters.type_id' => $secondary_monster_type->id,
									'Monsters.secondary_type_id' => $monster_type->id,
								]
							]
						])
						->contain([])
						->count();
					if($monster_check > 0) {
						$monster_type = null;
						$secondary_monster_type = null;
					}
				}
			}
			$type_id = $monster_type->id;
			$secondary_type_id = $secondary_monster_type->id;
		}


		$monster = $this->newEntity(
			[
				'user_id' => $user_id,
				'new' => 1,
				'name' => 'New Monster',
				'type_id' => $type_id,
				'secondary_type_id' => $secondary_type_id,
				'strength' => 1,
				'dexterity' => 1,
				'intelligence' => 1,
				'luck' => 1,
				'vitality' => 1,
				'elo_rating' => $elo_rating
			],
			['validate' => false]
		);
		$this->save($monster);
		return $monster;
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