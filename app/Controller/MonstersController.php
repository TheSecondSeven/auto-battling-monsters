<?php
App::uses('AppController', 'Controller');
/**
 * Monsters Controller
 *
 * @property Monster $Monster
 * @property PaginatorComponent $Paginator
 */
class MonstersController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session', 'Flash');
	
/**
 * Uses
 *
 * @var array
 */
	public $uses = [
		'Monster',
		'Skill',
		'Ultimate',
		'Augment',
		'SkillEffect',
		'UserSkill',
		'UserUltimate',
		'UserAugment',
		'Rune'
	];

	public function leaderboard() {
		$user_id = $this->Auth->user('id');
		$user = $this->User->find('first', [
			'conditions' => [
				'User.id' => $user_id,
			],
			'contain' => [
				'Monster' => [
					'Type',
					'SecondaryType',
					'Skill1',
					'Skill2',
					'Skill3',
					'Skill4',
					'Ultimate',
					'conditions' => [
						'Monster.skill_1_id != 0',
						'Monster.skill_2_id != 0',
						'Monster.skill_3_id != 0',
						'Monster.skill_4_id != 0',
						'Monster.ultimate_id != 0'
					],
					'order' => [
						'Monster.elo_rating DESC'
					]
				]
			]
		]);
		foreach($user['Monster'] as $monster_index=>$monster) {
			$user['Monster'][$monster_index]['placement'] = 1 + $this->Monster->find('count', [
				'conditions' => [
					'Monster.elo_rating >' => $monster['elo_rating']
				]
			]);
		}
		$this->set('user', $user);
		
		//top50
		$monsters = $this->Monster->find('all', [
			'conditions' => [
				'Monster.skill_1_id != 0',
				'Monster.skill_2_id != 0',
				'Monster.skill_3_id != 0',
				'Monster.skill_4_id != 0',
				'Monster.ultimate_id != 0'
			],
			'order' => [
				'Monster.elo_rating DESC'
			],
			'contain' => [
				'User',
				'Type',
				'SecondaryType',
				'Skill1',
				'Skill2',
				'Skill3',
				'Skill4',
				'Ultimate'
			],
			'limit' => 50
		]);
		$this->set('monsters', $monsters);
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Monster->recursive = 0;
		$this->set('monsters', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Monster->exists($id)) {
			throw new NotFoundException(__('Invalid monster'));
		}
		$options = array('conditions' => array('Monster.' . $this->Monster->primaryKey => $id));
		$this->set('monster', $this->Monster->find('first', $options));
	}
	public function edit($id = null) {
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
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Monster->save($this->request->data)) {
				$this->Flash->success(__('The monster has been updated.'));
				return $this->redirect(array('controller' => 'users', 'action' => 'my_monsters'));
			} else {
				$this->Flash->error(__('The monster could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Monster.' . $this->Monster->primaryKey => $id));
			$this->request->data = $this->Monster->find('first', $options);
		}
	}
	public function edit_runes($id = null) {
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
			$validated_runes = true;
			for($i=1; $i < 4; $i++) {
				$rune_check = $this->Rune->find('count', [
					'conditions' => [
						'Rune.user_id' => $user_id,
						'Rune.id' => $this->request->data['Monster']['rune_'.$i.'_id'],
						'NOT' => [
							'Rune.in_use_by_monster_id' => [0, $monster['Monster']['id']]
						]
					]
				]);
				if($rune_check > 0) {
					$this->Monster->invalidate('rune_'.$i.'_id', 'This rune is currently in use by another monster.');
					$validated_runes = false;
				}
			}
			if ($validated_runes && $this->Monster->save($this->request->data)) {
				
				//take all augments out of use from this monster
				$this->Monster->query('UPDATE `runes` SET `in_use_by_monster_id` = 0 WHERE `in_use_by_monster_id` = '.$monster['Monster']['id']);
				//set the in use
				for($i=1; $i < 4; $i++) {
					if(!empty($this->request->data['Monster']['rune_'.$i.'_id'])) {
						$this->Rune->id = $this->request->data['Monster']['rune_'.$i.'_id'];
						$this->Rune->saveField('in_use_by_monster_id', $monster['Monster']['id']);
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
		$runes = $this->Rune->find('all', [
			'conditions' => [
				'Rune.user_id' => $user_id,
				'Rune.in_use_by_monster_id' => [0, $monster['Monster']['id']]
			]
		]);
		if(empty($runes)) {
			$rune_options = [
				0 => 'You currently do not have any available Runes.'
			];
		}else{
			$rune_options = [0 => 'Choose a Rune'];
			foreach($runes as $rune) {
				$rune_options[$rune['Rune']['id']] = 'Level '.$rune['Rune']['level'].' '.$rune['Type']['name'].' Rune';
			}
		}
		$this->set('runes', $rune_options);
	}
	
	public function increase_rune_level($id = null) {
		$user_id = $this->Auth->user('id');
		$user = $this->User->findById($user_id);
		$monster = $this->Monster->find('first', [
			'conditions' => [
				'Monster.id' => $id,
				'Monster.user_id' => $user_id
			]
		]);
		if (empty($monster['Monster']['id'])) {
			throw new NotFoundException(__('Invalid monster'));
		}elseif ($monster['Monster']['rune_level'] >= 3) {
			throw new NotFoundException(__('Cannot increase runes for the Monster anymore.'));
		}
		if($monster['Monster']['rune_level'] == 1) {
			$cost = 250;
		}elseif($monster['Monster']['rune_level'] == 2) {
			$cost = 1000;
		}
		if($user['User']['gold'] < $cost) {
			$this->Flash->error(__('You do not have '.$cost.' Gold.'));
			return $this->redirect(['action' => 'edit_runes', $id]);
		}
		$this->User->id = $user['User']['id'];
		$this->User->saveField('gold', $user['User']['gold'] - $cost);
		$monster['Monster']['rune_level']++;
		$this->Monster->id = $monster['Monster']['id'];
		$this->Monster->saveField('rune_level', $monster['Monster']['rune_level']);
		$this->Flash->success(__($monster['Monster']['name'].' can now hold '.$monster['Monster']['rune_level'].' Runes'));
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
	public function edit_skills($id = null) {
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
			$validated_skills = true;
			for($i=1; $i < 5; $i++) {
				$skill_check = $this->UserSkill->find('count', [
					'conditions' => [
						'UserSkill.user_id' => $user_id,
						'UserSkill.skill_id' => $this->request->data['Monster']['skill_'.$i.'_id'],
						'NOT' => [
							'UserSkill.in_use_by_monster_id' => [0, $monster['Monster']['id']]
						]
					]
				]);
				if(!empty($skill_check)) {
					$this->Monster->invalidate('skill_'.$i.'_id', 'This skill is currently in use by another monster.');
					$validated_skills = false;
				}
			}
			$ultimate_check = $this->UserUltimate->find('count', [
				'conditions' => [
					'UserUltimate.user_id' => $user_id,
					'UserUltimate.ultimate_id' => $this->request->data['Monster']['ultimate_id'],
					'NOT' => [
						'UserUltimate.in_use_by_monster_id' => [0, $monster['Monster']['id']]
					]
				]
			]);
			if(!empty($ultimate_check)) {
				$this->Monster->invalidate('ultimate_id', 'This ultimate is currently in use by another monster.');
				$validated_skills = false;
			}
			
			if ($validated_skills && $this->Monster->save($this->request->data)) {
				//take all skills out of use from this monster
				$this->Monster->query('UPDATE `user_skills` SET `in_use_by_monster_id` = 0 WHERE `in_use_by_monster_id` = '.$monster['Monster']['id']);
				$this->Monster->query('UPDATE `user_ultimates` SET `in_use_by_monster_id` = 0 WHERE `in_use_by_monster_id` = '.$monster['Monster']['id']);
				//set the in use
				for($i=1; $i < 5; $i++) {
					if(!empty($this->request->data['Monster']['skill_'.$i.'_id'])) {
						$user_skill = $this->UserSkill->find('first', [
							'conditions' => [
								'UserSkill.user_id' => $user_id,
								'UserSkill.skill_id' => $this->request->data['Monster']['skill_'.$i.'_id'],
								'UserSkill.in_use_by_monster_id' => 0
							]
						]);
						$this->UserSkill->id = $user_skill['UserSkill']['id'];
						$this->UserSkill->saveField('in_use_by_monster_id', $monster['Monster']['id']);
					}
				}
				if(!empty($this->request->data['Monster']['ultimate_id'])) {
					$user_ultimate = $this->UserUltimate->find('first', [
						'conditions' => [
							'UserUltimate.user_id' => $user_id,
							'UserUltimate.ultimate_id' => $this->request->data['Monster']['ultimate_id'],
							'UserUltimate.in_use_by_monster_id' => 0
						]
					]);
					$this->UserUltimate->id = $user_ultimate['UserUltimate']['id'];
					$this->UserUltimate->saveField('in_use_by_monster_id', $monster['Monster']['id']);
				}
				$this->Flash->success(__('The monster has been updated.'));
				return $this->redirect(array('controller' => 'users', 'action' => 'my_monsters'));
			} else {
				$this->Flash->error(__('The monster could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Monster.' . $this->Monster->primaryKey => $id));
			$this->request->data = $this->Monster->find('first', $options);
		}
		$availableTypes = [
			4,
			$monster['Monster']['type_id']
		];
		if(!empty($monster['Monster']['secondary_type_id'])) {
			$availableTypes[] = $monster['Monster']['secondary_type_id'];
		}
			
		for($i=1; $i < 4; $i++) {
			if(!empty($monster['Rune'.$i]['id']) && $monster['Rune'.$i]['unlock_type'] == 1) {
				$availableTypes[] = $monster['Rune'.$i]['type_id'];
			}
		}
		$skills = $this->Skill->find('all', [
			'conditions' => [
				'Skill.type_id' => $availableTypes,
				'Skill.id' => $this->UserSkill->find('list',[
					'conditions' => [
						'UserSkill.user_id' => $user_id,
						'UserSkill.in_use_by_monster_id' => [0, $monster['Monster']['id']]
					],
					'fields' => [
						'UserSkill.skill_id'
					]
				])
			],
			'order' => [
				'Skill.type_id'
			]
		]);
		$skill_options = [];
		foreach($skills as $skill) {
			$skill_options[$skill['Skill']['id']] = $skill['Type']['name'].': '.$skill['Skill']['name'].' - '.$skill['Skill']['description'];
		}
		if(empty($skill_options)) {
			$skill_options[0] = 'You currently do not have any skills available for use in this slot.';
		}else{
			$skill_options = [0 => 'Select a Skill'] + $skill_options;
		}
		$this->set('skill_options', $skill_options);
		$ultimates = $this->Ultimate->find('all', [
			'conditions' => [
				'OR' => [
					'Ultimate.type_id' => $availableTypes,
					'Ultimate.secondary_type_id' => $availableTypes
				],
				'Ultimate.id' => $this->UserUltimate->find('list',[
					'conditions' => [
						'UserUltimate.user_id' => $user_id,
						'UserUltimate.in_use_by_monster_id' => [0, $monster['Monster']['id']]
					],
					'fields' => [
						'UserUltimate.ultimate_id'
					]
				])
			],
			'order' => [
				'Ultimate.type_id'
			]
		]);
		$ultimate_options = [];
		foreach($ultimates as $ultimate) {
			$ultimate_options[$ultimate['Ultimate']['id']] = $ultimate['Type']['name'].'/'.$ultimate['SecondaryType']['name'].': '.$ultimate['Ultimate']['name'].' - '.$ultimate['Ultimate']['description'];
		}
		if(empty($ultimate_options)) {
			$ultimate_options[0] = 'You currently do not have any ultimates available for use.';
		}else{
			$ultimate_options = [0 => 'Select an Ultimate'] + $ultimate_options;
		}
		$this->set('ultimate_options', $ultimate_options);
	}

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->Monster->recursive = 0;
		$this->set('monsters', $this->Paginator->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		if (!$this->Monster->exists($id)) {
			throw new NotFoundException(__('Invalid monster'));
		}
		$options = array('conditions' => array('Monster.' . $this->Monster->primaryKey => $id));
		$this->set('monster', $this->Monster->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->Monster->create();
			if ($this->Monster->save($this->request->data)) {
				$this->Flash->success(__('The monster has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The monster could not be saved. Please, try again.'));
			}
		}
		$users = $this->Monster->User->find('list');
		$types = $this->Monster->Type->find('list');
		$skill1s = $this->Monster->Skill1->find('list', ['order' => ['Skill1.type_id']]);
		$skill2s = $this->Monster->Skill2->find('list', ['order' => ['Skill2.type_id']]);
		$skill3s = $this->Monster->Skill3->find('list', ['order' => ['Skill3.type_id']]);
		$skill4s = $this->Monster->Skill4->find('list', ['order' => ['Skill4.type_id']]);
		$ultimates = $this->Monster->Ultimate->find('list', ['order' => ['Ultimate.type_id']]);
		$this->set(compact('users', 'types', 'skill1s', 'skill2s', 'skill3s', 'skill4s','ultimates'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		if (!$this->Monster->exists($id)) {
			throw new NotFoundException(__('Invalid monster'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Monster->save($this->request->data)) {
				$this->Flash->success(__('The monster has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The monster could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Monster.' . $this->Monster->primaryKey => $id));
			$this->request->data = $this->Monster->find('first', $options);
		}
		$users = $this->Monster->User->find('list');
		$types = $this->Monster->Type->find('list');
		$skill1s = $this->Monster->Skill1->find('list', ['order' => ['Skill1.type_id']]);
		$skill2s = $this->Monster->Skill2->find('list', ['order' => ['Skill2.type_id']]);
		$skill3s = $this->Monster->Skill3->find('list', ['order' => ['Skill3.type_id']]);
		$skill4s = $this->Monster->Skill4->find('list', ['order' => ['Skill4.type_id']]);
		$ultimates = $this->Monster->Ultimate->find('list', ['order' => ['Ultimate.type_id']]);
		$this->set(compact('users', 'types', 'skill1s', 'skill2s', 'skill3s', 'skill4s', 'ultimates'));
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		$this->Monster->id = $id;
		if (!$this->Monster->exists()) {
			throw new NotFoundException(__('Invalid monster'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Monster->delete()) {
			$this->Flash->success(__('The monster has been deleted.'));
		} else {
			$this->Flash->error(__('The monster could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
