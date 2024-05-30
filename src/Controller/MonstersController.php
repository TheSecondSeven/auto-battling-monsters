<?php
declare(strict_types=1);
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\View\Exception\MissingTemplateException;
use Cake\Event\EventInterface;
use Cake\ORM\Query\SelectQuery;

/**
 * Static content controller
 *
 * This controller will render views from templates/Pages/
 *
 * @link https://book.cakephp.org/4/en/controllers/pages-controller.html
 */
class MonstersController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authentication->addUnauthenticatedActions([]);
    }

    public function leaderboard() {
		//top50
		$monsters = $this->Monsters->find()
            ->where([
				'Monsters.skill_1_id != 0',
				'Monsters.skill_2_id != 0',
				'Monsters.skill_3_id != 0',
				'Monsters.skill_4_id != 0',
				'Monsters.ultimate_id != 0'
			])
            ->order([
				'Monsters.elo_rating DESC'
			])
			->contain([
				'Users',
				'Types',
				'SecondaryTypes',
				'Skill1',
				'Skill2',
				'Skill3',
				'Skill4',
				'Ultimates'
			])
			->limit(50)
            ->all();
		$this->set('monsters', $monsters);
	}

    public function myMonsters() {
        $monsters = $this->fetchTable('Monsters')
            ->find()
            ->where([
				'Monsters.user_id' => $this->user->id
			])
            ->contain([
				'Types',
				'SecondaryTypes',
				'Rune1',
				'Rune2',
				'Rune3',
				'Skill1',
				'Skill2',
				'Skill3',
				'Skill4',
				'Ultimates'
			])
			->order([
				'Monsters.elo_rating DESC'
			])
            ->all();
		$monsters_active = 0;
		foreach($monsters as $monster) {
			if($monster->in_gauntlet_run) {
				$monsters_active++;
			}
		}
		if($monsters_active >= $this->user->active_monster_limit) {
			$battle_available = false;
		}else{
			$battle_available = true;
		}
		$this->set(compact(['monsters','battle_available']));
    }

	public function view($id = null) {
        $monster = $this->Monsters
            ->find()
            ->where([
                'Monsters.id' => $id
            ])
            ->firstOrFail();
		$this->set('monster', $monster);
	}

	public function edit($id = null) {
        $monster = $this->Monsters
            ->find()
            ->where([
                'Monsters.id' => $id,
                'Monsters.user_id' => $this->user->id
            ])
            ->firstOrFail();
		if($monster->in_gauntlet_run) {
			$this->Flash->error(__('You cannot edit monsters while they are in the Gauntlet.'));
			return $this->redirect(['action' => 'my-monsters']);
		}elseif($monster->resting_until && (int)$monster->resting_until->toUnixString() > time()) {
			$this->Flash->error(__('You cannot edit monsters while they are reseting.'));
			return $this->redirect(['action' => 'my-monsters']);
		}
		if ($this->request->is(array('post', 'put'))) {
            $monster = $this->Monsters->patchEntity($monster, $this->request->getData());
			if ($this->Monsters->save($monster)) {
				$this->Flash->success(__('The monster has been updated.'));
				return $this->redirect(['action' => 'my-monsters']);
			} else {
				$this->Flash->error(__('The monster could not be saved. Please, try again.'));
			}
		}
		$this->set('monster', $monster);
	}

	public function editRunes($id = null) {
        $monster = $this->Monsters
            ->find()
            ->where([
                'Monsters.id' => $id,
                'Monsters.user_id' => $this->user->id
            ])
            ->firstOrFail();
		if($monster->in_gauntlet_run) {
			$this->Flash->error(__('You cannot edit monsters while they are in the Gauntlet.'));
			return $this->redirect(['action' => 'my-monsters']);
		}elseif($monster->resting_until && (int)$monster->resting_until->toUnixString() > time()) {
			$this->Flash->error(__('You cannot edit monsters while they are reseting.'));
			return $this->redirect(['action' => 'my-monsters']);
		}
		if ($this->request->is(array('post', 'put'))) {
			$validated_runes = true;
            $rune_ids = [];
			for($i=1; $i < 4; $i++) {
                $rune_id = $this->request->getData()['rune_'.$i.'_id'];
                if(!empty($rune_id)) {
                    $rune_check = $this->Monsters->Rune1
                        ->find()
                        ->where([
                            'Rune1.user_id' => $this->user->id,
                            'Rune1.id' => $rune_id,
                            'NOT' => [
                                'Rune1.in_use_by_monster_id IN' => [0, $monster->id]
                            ]
                        ])
                        ->count();
                    if($rune_check > 0) {
                        $monster->setError('rune_'.$i.'_id', 'This rune is currently in use by another monster.');
                        $validated_runes = false;
                    }elseif(in_array($rune_id, $rune_ids)){
                        $monster->setError('rune_'.$i.'_id', 'You can\'t use the same rune twice.');
                        $validated_runes = false;
					}else{
						$rune_ids[] = $rune_id;
					}
                }
			}
            if($validated_runes) {
                $monster = $this->Monsters->patchEntity($monster, $this->request->getData());
                if ($this->Monsters->save($monster)) { 
                    $this->Monsters->Rune1->updateAll(
                        [
                            'in_use_by_monster_id' => 0
                        ],
                        [
                            'in_use_by_monster_id' => $monster->id
                        ]
                    );
					if(!empty($rune_ids)) {
						$this->Monsters->Rune1->updateAll(
							[
								'in_use_by_monster_id' => $monster->id
							],
							[
								'id IN' => $rune_ids,
								'user_id' => $this->user->id
							]
						);
					}
                    
                    $this->Flash->success(__('The monster\'s runes has been updated.'));
                    return $this->redirect(['action' => 'my-monsters']);
                } else {
                    $this->Flash->error(__('The monster could not be saved. Please, try again.'));
                }
            }
		}
		$runes = $this->Monsters->Rune1
            ->find()
            ->where([
				'Rune1.user_id' => $this->user->id,
				'Rune1.in_use_by_monster_id IN' => [0, $monster->id]
            ])
			->contain([
				'Types'
			])
            ->all()
			->toList();
		if(empty($runes)) {
			$rune_options = [
				0 => 'You currently do not have any available Runes.'
			];
		}else{
			$rune_options = [0 => 'Choose a Rune'];
			foreach($runes as $rune) {
				$features = [];
				if($rune->unlock_type) $features[] = 'Unlocks '.$rune->type->name.' Skills';
				if($rune->damage_level > 0) $features[] = $rune->type->name.' Damage Increased '.(RUNE_DAMAGE_INCREASE * $rune->damage_level).'%';
				if($rune->healing_level > 0) $features[] = $rune->type->name.' Healing Increased '.(RUNE_HEALING_INCREASE * $rune->healing_level).'%';
				if($rune->critical_chance_level > 0) $features[] = 'Critical Hits with '.$rune->type->name.' Abilities Increased '.(RUNE_CRITICAL_CHANCE_INCREASE * $rune->critical_chance_level).'%';
				if($rune->cast_again_level > 0) $features[] = $rune->type->name.' Skills Have a '.(RUNE_CAST_AGAIN_INCREASE * $rune->cast_again_level).'% to Cast Again';
				if($rune->casting_speed_level > 0) $features[] = 'Casting Speed of '.$rune->type->name.' Skills Increased '.(RUNE_CASTING_SPEED_INCREASE * $rune->casting_speed_level).'%';
				if($rune->health_level > 0) $features[] = 'Monster Health Increased '.(RUNE_HEALTH_INCREASE * $rune->health_level).'%';
				$rune_options[$rune->id] = 'Level '.$rune->level.' '.$rune->type->name.' Rune: '.implode(' | ', $features);
			}
		}
		$this->set('monster', $monster);
		$this->set('runes', $rune_options);
	}
	
	public function increaseRuneLevel($id = null) {
        $monster = $this->Monsters
            ->find()
            ->where([
                'Monsters.id' => $id,
                'Monsters.user_id' => $this->user->id
            ])
            ->firstOrFail();
		if ($monster->rune_level >= 3) {
			$this->Flash->error('Cannot increase runes for this Monster anymore.');
			return $this->redirect(['action' => 'edit_runes', $monster->id]);
		}
		if($monster->rune_level == 1) {
			$cost = 250;
		}elseif($monster->rune_level == 2) {
			$cost = 1000;
		}
		if($this->user->gold < $cost) {
			$this->Flash->error(__('You do not have '.$cost.' Gold.'));
			return $this->redirect(['action' => 'edit_runes', $monster->id]);
		}
		$monster->rune_level += 1;
		$this->Monsters->save($monster);
		$this->user = $this->Monsters->Users->patchEntity($this->user, ['gold' => $this->user->gold - $cost], ['validate' => false]);
		$this->Monsters->Users->save($this->user);
		$this->Authentication->setIdentity($this->user);
		$this->Flash->success(__($monster->name.' can now hold '.$monster->rune_level.' Runes'));
		return $this->redirect(['action' => 'edit_runes', $id]);
	}
	/*
	public function edit_augments($id = null) {
		$user_id = $this->Auth->user('id');
		$monster = $this->Monster->find('first', [
			'conditions' => [
				'Monster.id' => $id,
				'Monster.user_id' => $user_id
			]
		]);
		if (empty($monster['Monster']['id'])) {
			throw new NotFoundException(__('Invalid monster'));
		}elseif($monster['Monster']['in_gauntlet_run']) {
			$this->Flash->error(__('You cannot edit monsters while they are in the Gauntlet.'));
			return $this->redirect(array('controller' => 'users', 'action' => 'my_monsters'));
		}elseif(strtotime($monster['Monster']['resting_until']) > time()) {
			$this->Flash->error(__('You cannot edit monsters while they are reseting.'));
			return $this->redirect(array('controller' => 'users', 'action' => 'my_monsters'));
		}
		$this->set('monster', $monster);
		if ($this->request->is(array('post', 'put'))) {
			//validate you arent using a skill of another monster
			$validated_augments = true;
			foreach(['skill_1','skill_2','skill_3','skill_4','ultimate'] as $slot) {
				$augment_check = $this->UserAugment->find('count', [
					'conditions' => [
						'UserAugment.user_id' => $user_id,
						'UserAugment.augment_id' => $this->request->data['Monster']['augment_'.$slot.'_id'],
						'NOT' => [
							'UserAugment.in_use_by_monster_id' => [0, $monster['Monster']['id']]
						]
					]
				]);
				if(!empty($augment_check)) {
					$this->Monster->invalidate('augment_'.$slot.'_id', 'This augment is currently in use by another monster.');
					$validated_augments = false;
				}
			}
			
			if ($validated_augments && $this->Monster->save($this->request->data)) {
				
				//take all augments out of use from this monster
				$this->Monster->query('UPDATE `user_augments` SET `in_use_by_monster_id` = 0 WHERE `in_use_by_monster_id` = '.$monster['Monster']['id']);
				//set the in use
				foreach(['skill_1','skill_2','skill_3','skill_4','ultimate'] as $slot) {
					if(!empty($this->request->data['Monster']['augment_'.$slot.'_id'])) {
						$user_augment = $this->UserAugment->find('first', [
							'conditions' => [
								'UserAugment.user_id' => $user_id,
								'UserAugment.augment_id' => $this->request->data['Monster']['augment_'.$slot.'_id'],
								'UserAugment.in_use_by_monster_id' => 0
							]
						]);
						$this->UserAugment->id = $user_augment['UserAugment']['id'];
						$this->UserAugment->saveField('in_use_by_monster_id', $monster['Monster']['id']);
					}
				}
				$this->Flash->success(__('The monster\'s runes has been updated.'));
				return $this->redirect(array('controller' => 'users', 'action' => 'my_monsters'));
			} else {
				$this->Flash->error(__('The monster could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Monster.' . $this->Monster->primaryKey => $id));
			$this->request->data = $this->Monster->find('first', $options);
		}
		foreach(['skill_1','skill_2','skill_3','skill_4','ultimate'] as $slot) {
			$augments = $this->Augment->find('list', [
				'conditions' => [
					'Augment.'.$slot => 1,
					'Augment.id' => $this->UserAugment->find('list',[
						'conditions' => [
							'UserAugment.user_id' => $user_id,
							'UserAugment.in_use_by_monster_id' => [0, $monster['Monster']['id']]
						],
						'fields' => [
							'UserAugment.augment_id'
						]
					])
				]
			]);
			if(empty($augments)) {
				$translation = [
					'skill_1' => 'the 1st Skill',
					'skill_2' => 'the 2nd Skill',
					'skill_3' => 'the 3rd Skill',
					'skill_4' => 'the 4th Skill',
					'ultimate' => 'Ultimates'
				];
				$augments = [
					0 => 'You currently do not have any Runes for '.$translation[$slot]
				];
			}else{
				$augments = [0 => 'Choose a Rune'] + $augments;
			}
			$this->set($slot.'_augments', $augments);
		}
	}
	*/
	public function editMoveSet($id = null) {
        $monster = $this->Monsters
            ->find()
            ->where([
                'Monsters.id' => $id,
                'Monsters.user_id' => $this->user->id
            ])
			->contain([
				'Skill1',
				'Skill2',
				'Skill3',
				'Skill4',
				'Ultimates',
				'Rune1',
				'Rune2',
				'Rune3'
			])
            ->firstOrFail();
		if($monster->in_gauntlet_run) {
			$this->Flash->error(__('You cannot edit monsters while they are in the Gauntlet.'));
			return $this->redirect(['action' => 'my-monsters']);
		}elseif($monster->resting_until && (int)$monster->resting_until->toUnixString() > time()) {
			$this->Flash->error(__('You cannot edit monsters while they are reseting.'));
			return $this->redirect(['action' => 'my-monsters']);
		}
		if ($this->request->is(array('post', 'put'))) {
			//validate you arent using a skill of another monster
			$validated_skills = true;
			$skill_ids = [];
			for($i=1; $i < 5; $i++) {
				$skill_id = $this->request->getData()['skill_'.$i.'_id'];
				if($skill_id > 0) {
					$skill_check = $this->fetchTable('UserSkills')
						->find()
						->where([
							'UserSkills.user_id' => $this->user->id,
							'UserSkills.skill_id' => $skill_id,
							'NOT' => [
								'UserSkills.in_use_by_monster_id IN' => [0, $monster->id]
							]
						])
						->count();
					if(!empty($skill_check)) {
						$monster->setError('skill_'.$i.'_id', 'This skill is currently in use by another monster.');
						$validated_skills = false;
					}elseif(in_array($skill_id, $skill_ids)) {
						$monster->setError('skill_'.$i.'_id', 'This skill is currently in use by another monster.');
						$validated_skills = false;
					}else{
						$skill_ids[] = $skill_id;
					}
				}
			}
			$ultimate_check = $this->fetchTable('UserUltimates')
				->find()
				->where([
					'UserUltimates.user_id' => $this->user->id,
					'UserUltimates.ultimate_id' => $this->request->getData()['ultimate_id'],
					'NOT' => [
						'UserUltimates.in_use_by_monster_id IN' => [0, $monster->id]
					]
				])
				->count();
			if(!empty($ultimate_check)) {
				$monster->setError('ultimate_id', 'This ultimate is currently in use by another monster.');
				$validated_skills = false;
			}
			
			if ($validated_skills) {

                $monster = $this->Monsters->patchEntity($monster, $this->request->getData());
				if($this->Monsters->save($monster)) {
					//take all skills out of use from this monster
                    $this->fetchTable('UserSkills')->updateAll(
                        [
                            'in_use_by_monster_id' => 0
                        ],
                        [
                            'in_use_by_monster_id' => $monster->id
                        ]
                    );
					if(!empty($skill_ids)) {
						$this->fetchTable('UserSkills')->updateAll(
							[
								'in_use_by_monster_id' => $monster->id
							],
							[
								'skill_id IN' => $skill_ids,
								'user_id' => $this->user->id
							]
						);
					}
					$this->fetchTable('UserUltimates')->updateAll(
                        [
                            'in_use_by_monster_id' => 0
                        ],
                        [
                            'in_use_by_monster_id' => $monster->id
                        ]
                    );
					if(!empty($monster->ultimate_id)) {
						$this->fetchTable('UserUltimates')->updateAll(
							[
								'in_use_by_monster_id' => $monster->id
							],
							[
								'ultimate_id' => $monster->ultimate_id,
								'user_id' => $this->user->id
							]
						);
					}
					$this->Flash->success(__('The monster has been updated.'));
					return $this->redirect(['action' => 'my_monsters']);
				} else {
					$this->Flash->error(__('The monster could not be saved. Please, try again.'));
				}
			}
		}
		$availableTypes = [
			4,
			$monster->type_id
		];
		if(!empty($monster->secondary_type_id)) {
			$availableTypes[] = $monster->secondary_type_id;
		}
			
		for($i=1; $i < 4; $i++) {
			$rune = 'rune'.$i;
			if(!empty($monster->$rune->id) && $monster->$rune->unlock_type == 1) {
				$availableTypes[] = $monster->$rune->type_id;
			}
		}
		$user_id = $this->user->id;
		$skills = $this->Monsters->Skill1
			->find()
			->where([
				'Skill1.type_id IN' => $availableTypes
			])
			->matching('UserSkills', function ($q) use ($user_id, $monster) {
                return $q->where([
					'UserSkills.user_id' => $user_id,
					'UserSkills.in_use_by_monster_id IN' => [0, $monster->id]
				]);
            })
			->order([
				'Skill1.type_id'
			])
			->contain([
				'Types'
			])
			->all()
			->toList();
		$skill_options = [];
		foreach($skills as $skill) {
			$skill_options[$skill->id] = $skill->type->name.': '.$skill->name.' - '.$skill->description;
		}
		if(empty($skill_options)) {
			$skill_options[0] = 'You currently do not have any skills available for use in this slot.';
		}else{
			$skill_options = [0 => 'Select a Skill'] + $skill_options;
		}
		$this->set('skill_options', $skill_options);
		$ultimates = $this->Monsters->Ultimates
			->find()
			->where([
				'OR' => [
					'Ultimates.type_id IN' => $availableTypes,
					'Ultimates.secondary_type_id IN' => $availableTypes
				]
			])
			->matching('UserUltimates', function ($q) use ($user_id, $monster) {
                return $q->where([
					'UserUltimates.user_id' => $user_id,
					'UserUltimates.in_use_by_monster_id IN' => [0, $monster->id]
				]);
            })
			->order([
				'Ultimates.type_id'
			])
			->contain([
				'Types',
				'SecondaryTypes'
			])
			->all()
			->toList();
		$ultimate_options = [];
		foreach($ultimates as $ultimate) {
			$ultimate_options[$ultimate->id] = $ultimate->type->name.'/'.(!empty($ultimate->secondary_type) ? $ultimate->secondary_type->name : '').': '.$ultimate->name.' - '.$ultimate->description;
		}
		if(empty($ultimate_options)) {
			$ultimate_options[0] = 'You currently do not have any ultimates available for use.';
		}else{
			$ultimate_options = [0 => 'Select an Ultimate'] + $ultimate_options;
		}
		$this->set('ultimate_options', $ultimate_options);

		$all_skills = $this->fetchTable('Skills')
			->find()
			->where([
				'Skills.rarity !=' => 'Admin Only',
				'Skills.type_id IN' => $availableTypes
			])
			->contain('UserSkills', function (SelectQuery $q) use ($user_id) {
                    return $q
                        ->where([
                            'UserSkills.user_id' => $user_id
                        ]);
                })
			->contain([
				'Types'
			])
			->order([
				'Skills.rarity DESC'
			])
			->all()
			->toList();

		$all_ultimates = $this->fetchTable('Ultimates')
			->find()
			->where([
				'Ultimates.rarity !=' => 'Admin Only',
				'OR' => [
					'Ultimates.type_id IN' => $availableTypes,
					'Ultimates.secondary_type_id IN' => $availableTypes
				]
			])
			->contain([
				'Types',
				'SecondaryTypes'
			])
			->contain('UserUltimates', function (SelectQuery $q) use ($user_id) {
                    return $q
                        ->where([
                            'UserUltimates.user_id' => $user_id
                        ]);
                })
			->order([
				'Ultimates.rarity DESC'
			])
			->all()
			->toList();
		$all_moves = $all_ultimates + $all_skills;
		usort($all_moves, [$this, 'sort_by_rarity']);
		usort($all_moves, [$this, 'sort_by_owned']);
		
		$this->set(compact(['monster','all_moves']));
	}



	function sort_by_owned($a, $b)
	{
		if ($a->owned == $b->owned) {
			return 0;
		}
		return ($a->owned > $b->owned) ? -1 : 1;
	}

	function sort_by_rarity($a, $b)
	{
		$rarities = [
			'Common' => 1,
			'Uncommon' => 2,
			'Rare' => 3,
			'Epic' => 4,
			'Legendary' => 5
		];
		if ($a->rarity == $b->rarity) {
			return 0;
		}
		return ($rarities[$a->rarity] > $rarities[$b->rarity]) ? -1 : 1;
	}
	
}