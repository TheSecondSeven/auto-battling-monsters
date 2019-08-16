<?php
App::uses('AppController', 'Controller');
/**
 * Users Controller
 *
 * @property User $User
 * @property PaginatorComponent $Paginator
 */
class UsersController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session', 'Flash');
	public $uses = [
		'User',
		'Type',
		'Monster',
		'UserSkill',
		'UserUltimate',
		'Skill',
		'Ultimate',
		'Rune'
	];
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(array('login', 'logout', 'register'));
	}
/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->User->recursive = 0;
		$this->set('users', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
		$this->set('user', $this->User->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function register() {
		if ($this->request->is('post')) {
			$this->User->create();
			
			$this->User->set($this->request->data);
			if($this->User->validates()) {
			
				$types = $this->Type->find('all');
				$type_counts = [];
				foreach($types as $type) {
					$type_counts[$type['Type']['name']] = 0;
				}
				foreach($this->User->answer_values[$this->request->data['User']['favorite_dessert']] as $type) {
					$type_counts[$type]++;
				}
				foreach($this->User->answer_values[$this->request->data['User']['favorite_power_ranger']] as $type) {
					$type_counts[$type]++;
				}
				foreach($this->User->answer_values[$this->request->data['User']['favorite_nineties_cartoon']] as $type) {
					$type_counts[$type]++;
				}
				foreach($this->User->answer_values[$this->request->data['User']['favorite_pizza_topping']] as $type) {
					$type_counts[$type]++;
				}
				foreach($this->User->answer_values[$this->request->data['User']['favorite_day_of_the_week']] as $type) {
					$type_counts[$type]++;
				}
				foreach($this->User->answer_values[$this->request->data['User']['favorite_rpg_class']] as $type) {
					$type_counts[$type]++;
				}
				//disable flying for now
				unset($type_counts['Flying']);
				arsort($type_counts);
				$this->request->data['User']['password'] = $this->request->data['User']['pwd'];
				$this->User->create();
				if ($this->User->save($this->request->data)) {
					$user = $this->User->read();
					
					$type_name = array_keys($type_counts)[0];
					$type_2_name = array_keys($type_counts)[1];
					$type_id = 1;
					$type_2_id = 2;
					foreach($types as $type) {
						if($type['Type']['name'] == $type_name) {
							$type_id = $type['Type']['id'];
						}
						if($type['Type']['name'] == $type_2_name) {
							$type_2_id = $type['Type']['id'];
						}
					}
					
					//find starting elo
					/*
					$lowest_monster = $this->Monster->find('first', [
						'order' => [
							'Monster.elo_rating ASC'
						]
					]);
					
					if(empty($lowest_monster['Monster']['elo_rating'])) {
						$lowest_monster['Monster']['elo_rating'] = 2100;
					}
					*/
					
					//create monster 
					$this->Monster->createMonster($user['User']['id'], $type_id, 2100);
					//create second monster 
					$this->Monster->createMonster($user['User']['id'], $type_2_id, 2100);
					
					//grant base skills
					$base_skills = [
						2,
						56,
						57,
						58
					];
					foreach($base_skills as $base_skill_id) {
						$this->UserSkill->create();
						$user_skill['UserSkill'] = [
							'id' => null,
							'user_id' => $user['User']['id'],
							'skill_id' => $base_skill_id
						];
						$this->UserSkill->save($user_skill);
					}
					foreach($types as $type) {
						if($type['Type']['name'] != 'Neutral') {
							//grant 1 skill of type_id
							$random_common_skill = $this->Skill->find('first', [
								'conditions' => [
									'Skill.type_id' => $type['Type']['id'],
									'Skill.rarity' => 'Uncommon'
								],
								'order' => [
									'RAND()'
								]
							]);
							if(!empty($random_common_skill)) {
								$this->UserSkill->create();
								$user_skill['UserSkill'] = [
									'id' => null,
									'user_id' => $user['User']['id'],
									'skill_id' => $random_common_skill['Skill']['id']
								];
								$this->UserSkill->save($user_skill);
							}
						}
					}
					
					//give hyperbeam
					$this->UserUltimate->create();
					$user_ultimate['UserUltimate'] = [
						'id' => null,
						'user_id' => $user['User']['id'],
						'ultimate_id' => 1
					];
					$this->UserUltimate->save($user_ultimate);
					
					//give holy nova
					$this->UserUltimate->create();
					$user_ultimate['UserUltimate'] = [
						'id' => null,
						'user_id' => $user['User']['id'],
						'ultimate_id' => 16
					];
					$this->UserUltimate->save($user_ultimate);
					
					unset($user['User']['password']);
					$this->Auth->login($user['User']);
					$this->Flash->success(__('Registration Complete.'));
					return $this->redirect('/new/');
				} else {
					$this->Flash->error(__('There was an error with registration. Please, try again.'));
				}
			}
		}
		exit;
	}
	
	public function admin_reset_all() {
		$users = $this->User->find('all', [
			'conditions' => [
				'User.type' => 'User'
			]
		]);
		foreach($users as $user) {
			
			$this->User->id = $user['User']['id'];
			$this->User->saveField('rune_shards', 5);
			$this->User->saveField('gold', 0);
			$this->User->saveField('gems', 25);
			$this->User->saveField('active_monster_limit', 1);
			
			$this->User->query('DELETE FROM `runes` WHERE `user_id` = '.$user['User']['id']);
			$this->User->query('DELETE FROM `user_skills` WHERE `user_id` = '.$user['User']['id']);
			$this->User->query('DELETE FROM `user_ultimates` WHERE `user_id` = '.$user['User']['id']);
			
			
			
			$types = $this->Type->find('all');
			$type_counts = [];
			foreach($types as $type) {
				$type_counts[$type['Type']['name']] = 0;
			}
			foreach($this->User->answer_values[$user['User']['favorite_dessert']] as $type) {
				$type_counts[$type]++;
			}
			foreach($this->User->answer_values[$user['User']['favorite_power_ranger']] as $type) {
				$type_counts[$type]++;
			}
			foreach($this->User->answer_values[$user['User']['favorite_nineties_cartoon']] as $type) {
				$type_counts[$type]++;
			}
			foreach($this->User->answer_values[$user['User']['favorite_pizza_topping']] as $type) {
				$type_counts[$type]++;
			}
			foreach($this->User->answer_values[$user['User']['favorite_day_of_the_week']] as $type) {
				$type_counts[$type]++;
			}
			foreach($this->User->answer_values[$user['User']['favorite_rpg_class']] as $type) {
				$type_counts[$type]++;
			}
			//disable flying for now
			unset($type_counts['Flying']);
			arsort($type_counts);
			
			
			$type_name = array_keys($type_counts)[0];
			$type_2_name = array_keys($type_counts)[1];
			$type_id = 1;
			$type_2_id = 2;
			foreach($types as $type) {
				if($type['Type']['name'] == $type_name) {
					$type_id = $type['Type']['id'];
				}
				if($type['Type']['name'] == $type_2_name) {
					$type_2_id = $type['Type']['id'];
				}
			}
			
			//find starting elo
			/*
			$lowest_monster = $this->Monster->find('first', [
				'order' => [
					'Monster.elo_rating ASC'
				]
			]);
			
			if(empty($lowest_monster['Monster']['elo_rating'])) {
				$lowest_monster['Monster']['elo_rating'] = 2100;
			}
			*/
			
			//create monster 
			$this->Monster->createMonster($user['User']['id'], $type_id, 2100);
			//create second monster 
			$this->Monster->createMonster($user['User']['id'], $type_2_id, 2100);
			
			//grant base skills
			$base_skills = [
				2,
				56,
				57,
				58
			];
			foreach($base_skills as $base_skill_id) {
				$this->UserSkill->create();
				$user_skill['UserSkill'] = [
					'id' => null,
					'user_id' => $user['User']['id'],
					'skill_id' => $base_skill_id
				];
				$this->UserSkill->save($user_skill);
			}
			foreach($types as $type) {
				if($type['Type']['name'] != 'Neutral') {
					//grant 1 skill of type_id
					$random_common_skill = $this->Skill->find('first', [
						'conditions' => [
							'Skill.type_id' => $type['Type']['id'],
							'Skill.rarity' => 'Uncommon'
						],
						'order' => [
							'RAND()'
						]
					]);
					if(!empty($random_common_skill)) {
						$this->UserSkill->create();
						$user_skill['UserSkill'] = [
							'id' => null,
							'user_id' => $user['User']['id'],
							'skill_id' => $random_common_skill['Skill']['id']
						];
						$this->UserSkill->save($user_skill);
					}
				}
			}
			
			//give hyperbeam
			$this->UserUltimate->create();
			$user_ultimate['UserUltimate'] = [
				'id' => null,
				'user_id' => $user['User']['id'],
				'ultimate_id' => 1
			];
			$this->UserUltimate->save($user_ultimate);
			
			//give holy nova
			$this->UserUltimate->create();
			$user_ultimate['UserUltimate'] = [
				'id' => null,
				'user_id' => $user['User']['id'],
				'ultimate_id' => 16
			];
			$this->UserUltimate->save($user_ultimate);
		}
	}
	
	public function login() {
	    if ($this->request->is('post')) {
	        if ($this->Auth->login()) {
	            return $this->redirect($this->Auth->redirectUrl());
	        }
	        $this->Flash->error(
	            __('Username or password is incorrect')
	        );
	    }
	}
	public function logout() {
    	return $this->redirect($this->Auth->logout());
	}
	
	
	public function admin_login() {
	    if ($this->request->is('post')) {
	        if ($this->Auth->login()) {
		        if($this->Auth->user('type') != 'Admin') {
			        $this->Flash->error(
			            __('You are not an Admin.')
			        );
			        $this->Auth->logout();
		        }else{
	            	return $this->redirect($this->Auth->redirectUrl());
		        }
	        }
	        $this->Flash->error(
	            __('Username or password is incorrect')
	        );
	    }
	}
	public function admin_logout() {
    	return $this->redirect($this->Auth->logout());
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is(array('post', 'put'))) {
			
			
			if ($this->User->save($this->request->data)) {
				$this->Flash->success(__('The user has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The user could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
			$this->request->data = $this->User->find('first', $options);
		}
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->User->delete()) {
			$this->Flash->success(__('The user has been deleted.'));
		} else {
			$this->Flash->error(__('The user could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->User->recursive = 0;
		$this->set('users', $this->Paginator->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
		$this->set('user', $this->User->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->User->create();
			if ($this->User->save($this->request->data)) {
				$this->Flash->success(__('The user has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The user could not be saved. Please, try again.'));
			}
		}
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->User->save($this->request->data)) {
				$this->Flash->success(__('The user has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The user could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
			$this->request->data = $this->User->find('first', $options);
		}
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->User->delete()) {
			$this->Flash->success(__('The user has been deleted.'));
		} else {
			$this->Flash->error(__('The user could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
	
	
	
	public function new_stuff() {
		$user_id = $this->Auth->user('id');
		$new_skills = $this->UserSkill->find('all', [
			'conditions' => [
				'UserSkill.user_id' => $user_id,
				'UserSkill.new' => 1
			],
			'contain' => [
				'Skill' => [
					'Type'
				]
			],
			'recursive' => 2
		]);
		foreach($new_skills as $new_skill) {
			$this->UserSkill->id = $new_skill['UserSkill']['id'];
			$this->UserSkill->saveField('new', 0);
		}
		$new_ultimates = $this->UserUltimate->find('all', [
			'conditions' => [
				'UserUltimate.user_id' => $user_id,
				'UserUltimate.new' => 1
			],
			'contain' => [
				'Ultimate' => [
					'Type'
				]
			],
			'recursive' => 2
		]);
		foreach($new_ultimates as $new_ultimate) {
			$this->UserUltimate->id = $new_ultimate['UserUltimate']['id'];
			$this->UserUltimate->saveField('new', 0);
		}
		$new_monsters = $this->Monster->find('all', [
			'conditions' => [
				'Monster.user_id' => $user_id,
				'Monster.new' => 1
			],
			'contain' => [
				'Type',
				'SecondaryType'
			],
			'recursive' => 2
		]);
		foreach($new_monsters as $new_monster) {
			$this->Monster->id = $new_monster['Monster']['id'];
			$this->Monster->saveField('new', 0);
		}
		
		$this->set(compact('new_skills','new_ultimates','new_monsters'));
	}
	
	
	public function my_monsters() {
		$user_id = $this->Auth->user('id');
		$monsters = $this->Monster->find('all', [
			'conditions' => [
				'Monster.user_id' => $user_id
			],
			'contain' => [
				'Type',
				'SecondaryType',
				'Rune1',
				'Rune2',
				'Rune3',
				'Skill1',
				'Skill2',
				'Skill3',
				'Skill4',
				'Ultimate'
			],
			'recursive' => 2
		]);
		$user = $this->User->findById($user_id);
		$monsters_active = 0;
		foreach($monsters as $monster) {
			if($monster['Monster']['in_gauntlet_run']) {
				$monsters_active++;
			}
		}
		if($monsters_active >= $user['User']['active_monster_limit']) {
			$battle_available = false;
		}else{
			$battle_available = true;
		}
		$this->set('user', $user);
		$this->set('battle_available', $battle_available);
		$this->set('monsters', $monsters);
	}
	
	public function purchase_random_single_type_monster() {
		$user_id = $this->Auth->user('id');
		$user = $this->User->findById($user_id);
		if($user['User']['gold'] < 250) {
			$this->Flash->error(__('You do not have 250 Gold. Battle in the Gauntlet to get some!'));
			return $this->redirect(['action' => 'my_monsters']);
		}
		$this->User->id = $user['User']['id'];
		$this->User->saveField('gold', $user['User']['gold'] - 250);
		
		$types = $this->Type->find('all', [
			'conditions' => [
				'Type.name != "Neutral"',
				'Type.name != "Flying"',
			]
		]);
		
		//check if they already have one of every type
		$doesnt_have_types = $this->Type->find('all', [
			'conditions' => [
				'Type.name != "Neutral"',
				'Type.name != "Flying"',
				'Type.id NOT IN (SELECT `type_id` FROM `monsters` WHERE `user_id` = '.$user_id.')'
			]
		]);
		
		$monster_type = null;
		
		while($monster_type == null) {
			if(!empty($doesnt_have_types)) {
				$monster_type = $doesnt_have_types[rand(0,count($doesnt_have_types) - 1)];
			}else{
				$monster_type = $types[rand(0,count($types) - 1)];
			}
			
			if(!empty($doesnt_have_types)) {
				//check if they have this monster
				$monster_check = $this->Monster->find('count', [
					'conditions' => [
						'Monster.user_id' => $user_id,
						'Monster.type_id' => $monster_type['Type']['id']
					],
					'recursive' => -1
				]);
				if($monster_check > 0) {
					$monster_type = null;
					$doesnt_have_types = null;
				}
			}
		}
		$this->Monster->createMonster($user_id, $monster_type['Type']['id']);
		$this->Flash->success(__('You gained a new '.$monster_type['Type']['name'].' Type Monster!'));
		return $this->redirect(['action' => 'my_monsters']);
	}
	
	public function purchase_random_dual_type_monster() {
		$user_id = $this->Auth->user('id');
		$user = $this->User->findById($user_id);
		if($user['User']['gold'] < 1000) {
			$this->Flash->error(__('You do not have 1000 Gold. Battle in the Gauntlet to get some!'));
			return $this->redirect(['action' => 'my_monsters']);
		}
		$this->User->id = $user['User']['id'];
		$this->User->saveField('gold', $user['User']['gold'] - 1000);
		
		$types = $this->Type->find('all', [
			'conditions' => [
				'Type.name != "Neutral"'
			]
		]);
		
		//check if they have all combos
		$dual_monster_count = $this->Monster->find('count', [
			'conditions' => [
				'Monster.user_id' => $user_id,
				'Monster.type_id != 0',
				'Monster.secondary_type_id != 0'
			],
			'recursive' => -1
		]);
		$type_count = count($types);
		$has_all_combos = false;
		if($dual_monster_count >= gmp_fact($type_count) / (2 * gmp_fact($type_count - 2))) {
			$has_all_combos = true;
		}
		
		$monster_type = null;
		$secondary_monster_type = null;
		while($monster_type == null) {
			$monster_type = $types[rand(0,count($types) - 1)];
			$secondary_monster_type = null;
			while($secondary_monster_type == 0) {
				$secondary_monster_type = $types[rand(0,count($types) - 1)];
				if($monster_type['Type']['id'] == $secondary_monster_type['Type']['id']) {
					$secondary_monster_type = null;
				}
			}
			if($has_all_combos == false) {
				//check if they have this monster
				$monster_check = $this->Monster->find('count', [
					'conditions' => [
						'Monster.user_id' => $user_id,
						'Monster.type_id' => $monster_type['Type']['id'],
						'Monster.secondary_type_id' => $secondary_monster_type['Type']['id']
					],
					'recursive' => -1
				]);
				$opposite_monster_check = $this->Monster->find('count', [
					'conditions' => [
						'Monster.user_id' => $user_id,
						'Monster.type_id' => $secondary_monster_type['Type']['id'],
						'Monster.secondary_type_id' => $monster_type['Type']['id']
					],
					'recursive' => -1
				]);
				if($monster_check > 0 || $opposite_monster_check > 0) {
					$monster_type = null;
					$secondary_monster_type = null;
				}
			}
		}
		
		$this->Monster->createMonster($user_id, $monster_type['Type']['id'], $secondary_monster_type['Type']['id']);
		$this->Flash->success(__('You gained a new '.$monster_type['Type']['name'].' and '.$secondary_monster_type['Type']['name'].' Dual Type Monster!'));
		return $this->redirect(['action' => 'my_monsters']);
	}
	
	public function increase_active_monster_limit() {
		$user_id = $this->Auth->user('id');
		$user = $this->User->findById($user_id);
		$cost = 50 * $user['User']['active_monster_limit'];
		if($user['User']['gems'] < $cost) {
			$this->Flash->error(__('You do not have '.$cost.' Gems.'));
			return $this->redirect(['action' => 'my_monsters']);
		}
		$this->User->id = $user['User']['id'];
		$this->User->saveField('gems', $user['User']['gems'] - $cost);
		$user['User']['active_monster_limit']++;
		$this->User->saveField('active_monster_limit', $user['User']['active_monster_limit']);
		$this->Flash->success(__('You can now have '.$user['User']['active_monster_limit'].' Monsters active in the Gauntlet at a time!'));
		return $this->redirect(['action' => 'my_monsters']);
	}
	
	public function my_skills() {
		$user_id = $this->Auth->user('id');
		$this->Paginator->settings = [
			'Skill' => [
				'order' => [
					'Skill.type_id'
				]
			]
		];
		$this->set('skills', $this->Paginator->paginate('Skill', [
			'Skill.id' => $this->UserSkill->find('list', [
				'conditions' => [
					'UserSkill.user_id' => $user_id
				],
				'fields' => [
					'UserSkill.skill_id'
				]
			])
		]));
	}
	
	public function my_ultimates() {
		$user_id = $this->Auth->user('id');
		$this->Paginator->settings = [
			'Ultimate' => [
				'order' => [
					'Ultimate.type_id'
				]
			]
		];
		$this->set('ultimates', $this->Paginator->paginate('Ultimate', [
			'Ultimate.id' => $this->UserUltimate->find('list', [
				'conditions' => [
					'UserUltimate.user_id' => $user_id
				],
				'fields' => [
					'UserUltimate.ultimate_id'
				]
			])
		]));
	}
	
	public function my_runes() {
		$user_id = $this->Auth->user('id');
		$this->Paginator->settings = [
			'Rune' => [
				'order' => [
					'Rune.level DESC',
					'Rune.type_id'
				]
			]
		];
		$this->set('runes', $this->Paginator->paginate('Rune', [
			'Rune.user_id' => $user_id
		]));
	}
	
	public function my_augments() {
		$user_id = $this->Auth->user('id');
		$this->Paginator->settings = [
			'Augment' => [
				'order' => [
					'Augment.rarity'
				]
			]
		];
		$this->set('augments', $this->Paginator->paginate('Augment', [
			'Augment.id' => $this->UserAugment->find('list', [
				'conditions' => [
					'UserAugment.user_id' => $user_id
				],
				'fields' => [
					'UserAugment.augment_id'
				]
			])
		]));
	}
}
