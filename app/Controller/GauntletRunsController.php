<?php
App::uses('AppController', 'Controller');
/**
 * Monsters Controller
 *
 * @property Monster $Monster
 * @property PaginatorComponent $Paginator
 */
class GauntletRunsController extends AppController {
/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session', 'Flash');
	
	
	public function beforeFilter() {
		parent::beforeFilter();
		/*$this->Auth->allow([
			'view'
		]);*/
	}
/**
 * Uses
 *
 * @var array
 */
	public $uses = [
		'User',
		'Monster',
		'Type',
		'Skill',
		'SkillEffect',
		'GauntletRun',
		'GauntletRunBattle',
		'Augment',
		'UserAugment',
		'Skill',
		'UserSkill',
		'Ultimate',
		'UserUltimate',
		'AvailableReward',
		'Status',
		'GauntletRunReward'
	];
/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->Paginator->settings = [
			'GauntletRun' => [
				'order' => [
					'GauntletRun.created DESC'
				]
			]
		];
		$this->GauntletRun->recursive = 0;
		$this->set('gauntlet_runs', $this->Paginator->paginate('GauntletRun'));
	}
	
	public function completed() {
		$user_id = $this->Auth->user('id');
		$this->Paginator->settings = [
			'GauntletRun' => [
				'order' => [
					'GauntletRun.created DESC'
				]
			]
		];
		$this->GauntletRun->recursive = 0;
		$this->set('gauntlet_runs', $this->Paginator->paginate('GauntletRun', [
				'GauntletRun.user_id' => $user_id
		]));
	}
	
	public function start_run($monster_id = null) {
		$user_id = $this->Auth->user('id');
		
		$monsters = $this->Monster->find('all', [
			'conditions' => [
				'Monster.user_id' => $user_id
			],
			'contain' => [
				'Type',
				'SecondaryType',
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
			$this->Flash->error(__('You can only have '.$user['User']['active_monster_limit'].' Monster'.($user['User']['active_monster_limit'] == 1 ? '' : 's').' active in the Gauntlet at a time.'));
			return $this->redirect(['controller' => 'users', 'action' => 'my_monsters']);
		}
		
		
		$monster = $this->Monster->find('first', [
			'conditions' => [
				'Monster.id' => $monster_id,
				'Monster.user_id' => $user_id,
				'Monster.in_gauntlet_run' => 0,
				'Monster.skill_1_id != 0',
				'Monster.skill_2_id != 0',
				'Monster.skill_3_id != 0',
				'Monster.skill_4_id != 0',
				'Monster.ultimate_id != 0'
			]
		]);
		if (empty($monster['Monster']['id'])) {
			throw new NotFoundException(__('Invalid monster'));
		}
		$this->Monster->id = $monster['Monster']['id'];
		$this->Monster->saveField('in_gauntlet_run', 1);
		$in_gauntlet_run_until = date('Y-m-d H:i:s', time() + 60 * 30);
		$this->Monster->saveField('in_gauntlet_run_until', $in_gauntlet_run_until);
		$this->Flash->success(__($monster['Monster']['name'].' has started battling in the Gauntlet. It will be done at '.date('g:ia',strtotime($in_gauntlet_run_until)).' PST'));
		return $this->redirect(['controller' => 'users', 'action' => 'my_monsters']);
	}
	
	public function complete_run($monster_id = null) {
		$user_id = $this->Auth->user('id');
		$options = [
			'conditions' => [
				'Monster.id' => $monster_id
			],
			'contain' => [
				'Type',
				'Rune1',
				'Rune2',
				'Rune3',
				'Skill1' => [
					'SkillEffect' => [
						'SecondarySkillEffect',
						'conditions' => [
							'SkillEffect.skill_effect_id' => 0
						]
					],
					'Type'
				],
				'Skill2' => [
					'SkillEffect' => [
						'SecondarySkillEffect',
						'conditions' => [
							'SkillEffect.skill_effect_id' => 0
						]
					],
					'Type'
				],
				'Skill3' => [
					'SkillEffect' => [
						'SecondarySkillEffect',
						'conditions' => [
							'SkillEffect.skill_effect_id' => 0
						]
					],
					'Type'
				],
				'Skill4' => [
					'SkillEffect' => [
						'SecondarySkillEffect',
						'conditions' => [
							'SkillEffect.skill_effect_id' => 0
						]
						
					],
					'Type'
				],
				'Ultimate' => [
					'SkillEffect' => [
						'SecondarySkillEffect',
						'conditions' => [
							'SkillEffect.skill_effect_id' => 0
						]
					],
					'Type'
				]
			]
		];
		$monster = $this->Monster->find('first', $options);
		if (empty($monster['Monster']['id'])) {
			throw new NotFoundException(__('Invalid monster'));
		}elseif($monster['Monster']['user_id'] != $user_id) {
			throw new NotFoundException(__('Invalid monster'));
		}elseif(strtotime($monster['Monster']['resting_until']) > time()) {
			throw new NotFoundException(__('Monster is still fighting in the Gauntlet.'));
		}elseif(!$monster['Monster']['in_gauntlet_run']) {
			throw new NotFoundException(__('Monster wasn\'t in the Gauntlet.'));
		}
		//create run 
		$this->GauntletRun->create();
		$gauntlet_run['GauntletRun'] = [
			'id' => null,
			'user_id' => $user_id,
			'monster_id' => $monster_id,
			'skill_1_id' => $monster['Monster']['skill_1_id'],
			'skill_2_id' => $monster['Monster']['skill_2_id'],
			'skill_3_id' => $monster['Monster']['skill_3_id'],
			'skill_4_id' => $monster['Monster']['skill_4_id'],
			'ultimate_id' => $monster['Monster']['ultimate_id']
		];
		$this->GauntletRun->save($gauntlet_run);
		$gauntlet_run_id = $this->GauntletRun->id;
		$this->Monster->id = $monster['Monster']['id'];
		$this->Monster->saveField('in_gauntlet_run', 0);
		$resting_until = date('Y-m-d H:i:s', time() + 60 * 30);
		$this->Monster->saveField('resting_until', $resting_until);
		
		$already_fought_ids = [
			$monster['Monster']['id']
		];
		$options['order'] = [
			'rand()'
		];
		$wins = 0;
		$losses = 0;
		$ties = 0;
		$streak = 0;
		$longest_streak = 0;
		$hot_streaks = 0;
		$battles = 0;
		$elo_change = 0;
		while($battles < 10) {
			//find opponent
			unset($options['conditions']);
			$opponent = null;
			$elo_threshold = 1;
			while(empty($opponent['Monster']['id'])) {
				$options['conditions'] = [
					'NOT' => [
						'Monster.id' => $already_fought_ids
					],
					'Monster.skill_1_id != 0',
					'Monster.skill_2_id != 0',
					'Monster.skill_3_id != 0',
					'Monster.skill_4_id != 0',
					'Monster.ultimate_id != 0'
				];
				if($elo_threshold < 15) {
					$options['conditions'][] = 'Monster.user_id != '.$user_id;
				}
				if($elo_threshold < 10) {
					$elo_threshold_amount = $elo_threshold * ELO_CONSTANT;
					$options['conditions']['Monster.elo_rating >='] = $monster['Monster']['elo_rating'] - $elo_threshold_amount;
					$options['conditions']['Monster.elo_rating <='] = $monster['Monster']['elo_rating'] + $elo_threshold_amount;
				}
				$opponent = $this->Monster->find('first', $options);
				$elo_threshold++;
			}
			$already_fought_ids[] = $opponent['Monster']['id'];
			$result = $this->Combat->twoTeamCombat($monster, $opponent);
			
			
			
			//ELO System
			$monster_q = pow(10, $monster['Monster']['elo_rating'] / 400);
			$opponent_q = pow(10, $opponent['Monster']['elo_rating'] / 400);
			
			$monster_e = $monster_q / ($monster_q + $opponent_q);
			$opponent_e = $opponent_q / ($monster_q + $opponent_q);
			
			if($result['winning_id'] == 1) {
				$wins++;
				$streak++;
				if($streak == 5) {
					$hot_streaks++;
				}elseif($streak == 10) {
					$hot_streaks++;
				}
				if($streak > $longest_streak) {
					$longest_streak = $streak;
				}
 				$result_text = 'Win';
				$monster_s = 1;
				$opponent_s = 0;
			}elseif($result['winning_id'] == 2) {
				$losses++;
				$streak = 0;
				$result_text = 'Loss';
				$opponent_s = 1;
				$monster_s = 0;
			}else{
				$ties++;
				//dont break streaks on ties
				$result_text = 'Tie';
				$monster_s = 0.5;
				$opponent_s = 0.5;
			}
			
			//save battle
			$this->GauntletRunBattle->create();
			$gauntlet_run_battle['GauntletRunBattle'] = [
				'id' => null,
				'gauntlet_run_id' => $gauntlet_run_id,
				'user_id' => $user_id,
				'monster_id' => $monster['Monster']['id'],
				'opponent_id' => $opponent['Monster']['id'],
				'result' => $result_text,
				'monster_elo_rating' => $monster['Monster']['elo_rating'],
				'opponent_elo_rating' => $opponent['Monster']['elo_rating'],
				'result_json_data' => json_encode($result, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP),
				'order' => $battles
			];
			$this->GauntletRunBattle->save($gauntlet_run_battle);
			
			//$firstMonster
			$ratingChange = round(ELO_CONSTANT * ($monster_s - $monster_e));
			$monster['Monster']['elo_rating'] += $ratingChange;
			$elo_change += $ratingChange;
			$this->Monster->id = $monster['Monster']['id'];
			$this->Monster->saveField('elo_rating', $monster['Monster']['elo_rating']);
			$monster['Monster']['total_battles']++;
			$this->Monster->saveField('total_battles', $monster['Monster']['total_battles']);
			
			if($monster['Monster']['total_battles'] > 20) {
				//$secondMonster
				$ratingChange = round(ELO_CONSTANT * ($opponent_s - $opponent_e));
				$this->Monster->id = $opponent['Monster']['id'];
				$this->Monster->saveField('elo_rating', $opponent['Monster']['elo_rating'] + $ratingChange);
			}
			$battles++;
		}
		$guaranteed_rarity = null;
		if($wins >= 10) {
			$guaranteed_rarity = 'Legendary';
		}elseif($wins >= 7) {
			$guaranteed_rarity = 'Epic';
		}elseif($wins >= 5) {
			$guaranteed_rarity = 'Rare';
		}
		$number_of_rewards = 5;
		$number_of_reward_options = $wins + 1;
		$number_of_reward_picks = ceil($number_of_reward_options / 3);
		$this->GauntletRun->id = $gauntlet_run_id;
		$this->GauntletRun->saveField('wins', $wins);
		$this->GauntletRun->saveField('losses', $losses);
		$this->GauntletRun->saveField('ties', $ties);
		$this->GauntletRun->saveField('longest_streak', $longest_streak);
		$this->GauntletRun->saveField('hot_streaks', $hot_streaks);
		$this->GauntletRun->saveField('number_of_reward_picks', $number_of_reward_picks);
		$this->GauntletRun->saveField('number_of_reward_options', $number_of_reward_options);
		$this->GauntletRun->saveField('elo_change', $elo_change);
		
		$user = $this->User->findById($user_id);
		$user['User']['gold'] += 5 * $wins;
		$this->User->id = $user['User']['id'];
		$this->User->saveField('gold', $user['User']['gold']);
		if($wins > 0) {
			$flash_message = 'You won '.$wins.' time'.($wins == 1 ? '' : 's').' and earned '.(5 * $wins).' Gold.';
			if($guaranteed_rarity != null) {
				$flash_message .= ' You also earned a guaranteed '.$guaranteed_rarity;
				if($guaranteed_rarity != 'Legendary') {
					$flash_message .= ' or better';
				}
				$flash_message .= '.';
			}
		}else{
			$flash_message = 'It\'s dangerous to go alone! Take this.';
		}
		$this->Flash->success(__($flash_message));
		
		$rewards_currently = [];
		$attempts = 0;
		while(count($rewards_currently) < $number_of_rewards && $attempts < 1000) {
			$reward = $this->getReward($user_id, $rewards_currently, $guaranteed_rarity);
			if(!empty($reward)) {
				$guaranteed_rarity = null;
				$rewards_currently[] = $reward;
			}
			$attempts++;
		}
		foreach($rewards_currently as $reward) {
			$this->GauntletRunReward->create();
			$gauntlet_run_reward['GauntletRunReward'] = [
				'id' => null,
				'user_id' => $user_id,
				'gauntlet_run_id' => $gauntlet_run_id,
				'type' => $reward['type'],
				'rarity' => $reward['rarity']
			];
			if($reward['type'] == 'Skill') {
				$gauntlet_run_reward['GauntletRunReward']['skill_id'] = $reward['skill_id'];
			}elseif($reward['type'] == 'Ultimate') {
				$gauntlet_run_reward['GauntletRunReward']['ultimate_id'] = $reward['ultimate_id'];
			}elseif($reward['type'] == 'Gems' || $reward['type'] == 'Gold' || $reward['type'] == 'Rune Shards') {
				$gauntlet_run_reward['GauntletRunReward']['amount'] = $reward['amount'];
			}
			$this->GauntletRunReward->save($gauntlet_run_reward);
			$this->grantReward($gauntlet_run_reward['GauntletRunReward']);
		}
		return $this->redirect(['action' => 'view_results', $gauntlet_run_id]);
	}
	
	function view_battles($gauntlet_run_id) {
		$user_id = $this->Auth->user('id');
		$options = [
			'conditions' => [
				'GauntletRun.id' => $gauntlet_run_id
			],
			'contain' => [
				'Monster',
				'GauntletRunBattle' => [
					'Opponent'
				]
			]
		];
		$gauntlet_run = $this->GauntletRun->find('first', $options);
		if (empty($gauntlet_run['GauntletRun']['id'])) {
			throw new NotFoundException(__('Invalid run'));
		}elseif($gauntlet_run['GauntletRun']['user_id'] != $user_id && $this->Auth->user('type') != 'Admin') {
			throw new NotFoundException(__('Invalid run'));
		}
		$gauntletRunJSON = [];
		foreach($gauntlet_run['GauntletRunBattle'] as $gauntlet_run_battle) {
			$gauntletRunJSON[] = json_decode($gauntlet_run_battle['result_json_data'], true);
		}
		$this->set('battlesJSON', json_encode($gauntletRunJSON, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP));
		$this->set('gauntlet_run', $gauntlet_run);
		$this->set('statuses',$this->Status->find('all'));
	}
	function view_results($gauntlet_run_id) {$user_id = $this->Auth->user('id');
		$options = [
			'conditions' => [
				'GauntletRun.id' => $gauntlet_run_id
			],
			'contain' => [
				'GauntletRunBattle' => [
					'Opponent' => [
						'User'
					]
				],
				'GauntletRunReward' => [
					'Skill' => [
						'Type'
					],
					'Ultimate' => [
						'Type'
					]
				],
				'Monster'
			]
		];
		$gauntlet_run = $this->GauntletRun->find('first', $options);
		if (empty($gauntlet_run['GauntletRun']['id'])) {
			throw new NotFoundException(__('Invalid run'));
		}elseif($gauntlet_run['GauntletRun']['user_id'] != $user_id && $this->Auth->user('type') != 'Admin') {
			throw new NotFoundException(__('Invalid run'));
		}
		$this->set('gauntlet_run', $gauntlet_run);
	}
	
	private function getReward($user_id, &$rewards_currently, $at_least_rarity = null) {
		//get types for monsters
		$types = $this->Type->find('all');
		
		
		$roll = rand(1, 10000);
		$total = 10000;
		$legendary_chance = $total * 0.005;
		$epic_chance = $total * 0.01;
		$rare_chance = $total * 0.05;
		$uncommon_chance = $total * 0.30;
		$options = [];
		if($at_least_rarity == 'Legendary' || $roll > $total - $legendary_chance) {
			$rarity = 'Legendary';
		}elseif($at_least_rarity == 'Epic' || $roll > $total - $legendary_chance - $epic_chance) {
			$rarity = 'Epic';
		}elseif($at_least_rarity == 'Rare' || $roll > $total - $legendary_chance - $epic_chance - $rare_chance) {
			$rarity = 'Rare';
		}elseif($at_least_rarity == 'Uncommon' || $roll > $total - $legendary_chance - $epic_chance - $rare_chance - $uncommon_chance) {
			$rarity = 'Uncommon';
		}else{
			$rarity = 'Common';
		}
		//find skills 
		$dont_find_skills = [];
		foreach($rewards_currently as $reward) {
			if($reward['type'] == 'Skill') {
				$dont_find_skills[$reward['skill_id']] = $reward['skill_id'];
			}
		}
		$already_have_skills = $this->UserSkill->find('all',[
			'conditions' => [
				'UserSkill.user_id' => $user_id
			],
			'fields' => [
				'UserSkill.skill_id'
			]
		]);
		foreach($already_have_skills as $user_skill) {
			$dont_find_skills[$user_skill['UserSkill']['skill_id']] = $user_skill['UserSkill']['skill_id'];
		}
		$skills = $this->Skill->find('list', [
			'conditions' => [
				'NOT' => [
					'Skill.id' => $dont_find_skills
				],
				'Skill.rarity' => $rarity
			]
		]);
		foreach($skills as $skill_id => $skill) {
			$options[] = [
				'type' => 'Skill',
				'skill_id' => $skill_id
			];
		}
		//find ultimates 
		$dont_find_ultimates = [];
		foreach($rewards_currently as $reward) {
			if($reward['type'] == 'Ultimate') {
				$dont_find_ultimates[$reward['ultimate_id']] = $reward['ultimate_id'];
			}
		}
		$already_have_ultimates = $this->UserUltimate->find('all',[
			'conditions' => [
				'UserUltimate.user_id' => $user_id
			],
			'fields' => [
				'UserUltimate.ultimate_id'
			]
		]);
		foreach($already_have_ultimates as $user_ultimate) {
			$dont_find_ultimates[$user_ultimate['UserUltimate']['ultimate_id']] = $user_ultimate['UserUltimate']['ultimate_id'];
		}
		$ultimates = $this->Ultimate->find('list', [
			'conditions' => [
				'NOT' => [
					'Ultimate.id' => $dont_find_ultimates
				],
				'Ultimate.rarity' => $rarity
			]
		]);
		foreach($ultimates as $ultimate_id => $ultimate) {
			$options[] = [
				'type' => 'Ultimate',
				'ultimate_id' => $ultimate_id
			];
		}
		//populate gems gold and rune shards based off how many other rewards there are
		$total_options = count($options);
		for($i=0; $i<max(1,round($total_options / 3)); $i++) {
			if($rarity == 'Legendary') {
				/*
				$options[] = [
					'type' => 'Gold',
					'amount' => 1000
				];
				*/
				$options[] = [
					'type' => 'Gems',
					'amount' => 100
				];
				$options[] = [
					'type' => 'Rune Shards',
					'amount' => 5000
				];
			}elseif($rarity == 'Epic') {
				/*
				$options[] = [
					'type' => 'Gold',
					'amount' => 250
				];
				*/
				$options[] = [
					'type' => 'Gems',
					'amount' => 25
				];
				$options[] = [
					'type' => 'Rune Shards',
					'amount' => 1250
				];
			}elseif($rarity == 'Rare') {
				$options[] = [
					'type' => 'Gems',
					'amount' => 5
				];
				/*
				$options[] = [
					'type' => 'Gold',
					'amount' => 50
				];
				*/
				$options[] = [
					'type' => 'Rune Shards',
					'amount' => 250
				];
			}elseif($rarity == 'Uncommon') {
				$options[] = [
					'type' => 'Gems',
					'amount' => 1
				];
				/*
				$options[] = [
					'type' => 'Gold',
					'amount' => 10
				];
				*/
				$options[] = [
					'type' => 'Rune Shards',
					'amount' => 50
				];
			}elseif($rarity == 'Common') {
				/*
				$options[] = [
					'type' => 'Gold',
					'amount' => 1
				];
				*/
				$options[] = [
					'type' => 'Rune Shards',
					'amount' => 5
				];
			}
		}
		if(count($options) > 0) {
			$reward = $options[rand(0,count($options) - 1)];
			$reward['rarity'] = $rarity;
			return $reward;
		}else{
			return [];
		}
	}
	public function grantReward($gauntlet_run_reward) {
		$user_id = $this->Auth->user('id');
		if($gauntlet_run_reward['type'] == 'Skill') {
			$this->UserSkill->create();
			$user_skill['UserSkill'] = [
				'id' => null,
				'user_id' => $user_id,
				'skill_id' => $gauntlet_run_reward['skill_id']
			];
			$this->UserSkill->save($user_skill);
		}elseif($gauntlet_run_reward['type'] == 'Ultimate') {
			$this->UserUltimate->create();
			$user_ultimate['UserUltimate'] = [
				'id' => null,
				'user_id' => $user_id,
				'ultimate_id' => $gauntlet_run_reward['ultimate_id']
			];
			$this->UserUltimate->save($user_ultimate);
		}elseif($gauntlet_run_reward['type'] == 'Gems') {
			$user = $this->User->findById($user_id);
			$this->User->id = $user_id;
			$this->User->saveField('gems', $user['User']['gems'] + $gauntlet_run_reward['amount']);
		}elseif($gauntlet_run_reward['type'] == 'Gold') {
			$user = $this->User->findById($user_id);
			$this->User->id = $user_id;
			$this->User->saveField('gold', $user['User']['gold'] + $gauntlet_run_reward['amount']);
		}elseif($gauntlet_run_reward['type'] == 'Rune Shards') {
			$user = $this->User->findById($user_id);
			$this->User->id = $user_id;
			$this->User->saveField('rune_shards', $user['User']['rune_shards'] + $gauntlet_run_reward['amount']);
		}
		return true;
	}
	
	/*
	private function grantReward($user_id, &$rewards_currently, $at_least_rarity = null) {
		//get types for monsters
		$types = $this->Type->find('all');
		
		
		$roll = rand(1, 10000);
		$total = 10000;
		$legendary_chance = $total * 0.01;
		$epic_chance = $total * 0.04;
		$rare_chance = $total * 0.10;
		$uncommon_chance = $total * 0.30;
		$rarity = 'Common';
		$options = [];
		if($at_least_rarity == 'Legendary' || $roll > $total - $legendary_chance) {
			$rarity = 'Legendary';
			$options[] = [
				'type' => 'Gold',
				'amount' => 1000
			];
			$options[] = [
				'type' => 'Gems',
				'amount' => 100
			];
			$options[] = [
				'type' => 'Rune Shards',
				'amount' => 5000
			];
		}elseif($at_least_rarity == 'Epic' || $roll > $total - $legendary_chance - $epic_chance) {
			$rarity = 'Epic';
			$options[] = [
				'type' => 'Gold',
				'amount' => 250
			];
			$options[] = [
				'type' => 'Gems',
				'amount' => 25
			];
			$options[] = [
				'type' => 'Rune Shards',
				'amount' => 1250
			];
		}elseif($at_least_rarity == 'Rare' || $roll > $total - $legendary_chance - $epic_chance - $rare_chance) {
			$rarity = 'Rare';
			$options[] = [
				'type' => 'Gems',
				'amount' => 5
			];
			$options[] = [
				'type' => 'Gold',
				'amount' => 50
			];
			$options[] = [
				'type' => 'Rune Shards',
				'amount' => 250
			];
		}elseif($at_least_rarity == 'Uncommon' || $roll > $total - $legendary_chance - $epic_chance - $rare_chance - $uncommon_chance) {
			$rarity = 'Uncommon';
			$options[] = [
				'type' => 'Gems',
				'amount' => 5
			];
			$options[] = [
				'type' => 'Gold',
				'amount' => 10
			];
			$options[] = [
				'type' => 'Rune Shards',
				'amount' => 50
			];
		}else{
			$options[] = [
				'type' => 'Gold',
				'amount' => 1
			];
			$options[] = [
				'type' => 'Rune Shards',
				'amount' => 5
			];

		}
		//find skills 
		$dont_find_skills = [];
		foreach($rewards_currently as $reward) {
			if($reward['type'] == 'Skill') {
				$dont_find_skills[$reward['skill_id']] = $reward['skill_id'];
			}
		}
		$already_have_skills = $this->UserSkill->find('all',[
			'conditions' => [
				'UserSkill.user_id' => $user_id
			],
			'fields' => [
				'UserSkill.skill_id'
			]
		]);
		foreach($already_have_skills as $user_skill) {
			$dont_find_skills[$user_skill['UserSkill']['skill_id']] = $user_skill['UserSkill']['skill_id'];
		}
		$skills = $this->Skill->find('list', [
			'conditions' => [
				'NOT' => [
					'Skill.id' => $dont_find_skills
				],
				'Skill.rarity' => $rarity
			]
		]);
		foreach($skills as $skill_id => $skill) {
			$options[] = [
				'type' => 'Skill',
				'skill_id' => $skill_id
			];
		}
		//find ultimates 
		$dont_find_ultimates = [];
		foreach($rewards_currently as $reward) {
			if($reward['type'] == 'Ultimate') {
				$dont_find_ultimates[$reward['ultimate_id']] = $reward['ultimate_id'];
			}
		}
		$already_have_ultimates = $this->UserUltimate->find('all',[
			'conditions' => [
				'UserUltimate.user_id' => $user_id
			],
			'fields' => [
				'UserUltimate.ultimate_id'
			]
		]);
		foreach($already_have_ultimates as $user_ultimate) {
			$dont_find_ultimates[$user_ultimate['UserUltimate']['ultimate_id']] = $user_ultimate['UserUltimate']['ultimate_id'];
		}
		$ultimates = $this->Ultimate->find('list', [
			'conditions' => [
				'NOT' => [
					'Ultimate.id' => $dont_find_ultimates
				],
				'Ultimate.rarity' => $rarity
			]
		]);
		foreach($ultimates as $ultimate_id => $ultimate) {
			$options[] = [
				'type' => 'Ultimate',
				'ultimate_id' => $ultimate_id
			];
		}
		//find augments 
		$dont_find_augments = [];
		foreach($rewards_currently as $reward) {
			if($reward['type'] == 'Augment') {
				$dont_find_augments[$reward['augment_id']] = $reward['augment_id'];
			}
		}
		$already_have_augments = $this->UserAugment->find('all',[
			'conditions' => [
				'UserAugment.user_id' => $user_id
			],
			'fields' => [
				'UserAugment.augment_id'
			]
		]);
		foreach($already_have_augments as $user_augment) {
			$dont_find_augments[$user_augment['UserAugment']['augment_id']] = $user_augment['UserAugment']['augment_id'];
		}
		$augments = $this->Augment->find('list', [
			'conditions' => [
				'NOT' => [
					'Augment.id' => $dont_find_augments
				],
				'Augment.rarity' => $rarity
			]
		]);
		foreach($augments as $augment_id => $augment) {
			$options[] = [
				'type' => 'Augment',
				'augment_id' => $augment_id
			];
		}
		
		if(count($options) > 0) {
			$reward = $options[rand(0,count($options) - 1)];
			
			if($reward['type'] == 'Dual Type Monster') {
				$monster_not_made = true;
				$monster_types = $types;
				while($monster_not_made) {
					$random_index = rand(0,count($monster_types) - 1);
					$type_id = $monster_types[$random_index]['Type']['id'];
					$secondary_type_id = 0;
					while($secondary_type_id == 0) {
						$secondary_type_id = $monster_types[rand(0,count($monster_types) - 1)]['Type']['id'];
						if($type_id == $secondary_type_id) {
							$secondary_type_id = 0;
						}
					}
					$duplicate_found = false;
					foreach($rewards_currently as $reward_currently) {
						if($reward_currently['type'] == 'Dual Type Monster') {
							if(($type_id == $reward_currently['type_id'] && $secondary_type_id == $reward_currently['secondary_type_id']) || ($secondary_type_id == $reward_currently['type_id'] && $type_id == $reward_currently['secondary_type_id'])) {
								$duplicate_found = true;
								break;
							}
						}
					}
					//check if they have this monster
					$monster_check = $this->Monster->find('count', [
						'conditions' => [
							'Monster.user_id' => $user_id,
							'Monster.type_id' => $type_id,
							'Monster.secondary_type_id' => $secondary_type_id
						],
						'recursive' => -1
					]);
					$opposite_monster_check = $this->Monster->find('count', [
						'conditions' => [
							'Monster.user_id' => $user_id,
							'Monster.type_id' => $secondary_type_id,
							'Monster.secondary_type_id' => $type_id
						],
						'recursive' => -1
					]);
					if($monster_check > 0 || $opposite_monster_check > 0) {
						unset($monster_types[$random_index]);
						$monster_types = array_values($monster_types);
						if(count($monster_types) <= 1) {
							return [];
						}else{
							$duplicate_found = true;
						}
					}
					if(!$duplicate_found) {
						
						$monster_not_made = false;
						$reward['type_id'] = $type_id;
						$reward['secondary_type_id'] = $secondary_type_id;
						//remove any single type monster rewards of these types
						foreach($rewards_currently as $index=>$reward_currently) {
							if($reward_currently['type'] == 'Monster') {
								if($reward_currently['type_id'] == $type_id || $reward_currently['type_id'] == $secondary_type_id) {
									unset($rewards_currently[$index]);
								}
							}
						}
					}
				}
			}elseif($reward['type'] == 'Monster') {
				$monster_not_made = true;
				$monster_types = $types;
				while($monster_not_made) {
					$random_index = rand(0,count($monster_types) - 1);
					$type_id = $monster_types[$random_index]['Type']['id'];
					$duplicate_found = false;
					foreach($rewards_currently as $reward_currently) {
						if($reward_currently['type'] == 'Dual Type Monster') {
							if(($type_id == $reward_currently['type_id'] || $type_id == $reward_currently['secondary_type_id'])) {
								$duplicate_found = true;
								break;
							}
						}elseif($reward_currently['type'] == 'Monster') {
							if(($type_id == $reward_currently['type_id'])) {
								$duplicate_found = true;
								break;
							}
						}
					}
					//check if they have this monster
					$monster_check = $this->Monster->find('count', [
						'conditions' => [
							'Monster.user_id' => $user_id,
							'Monster.type_id' => $type_id
						],
						'recursive' => -1
					]);
					if($monster_check > 0) {
						unset($monster_types[$random_index]);
						$monster_types = array_values($monster_types);
						if(count($monster_types) <= 0) {
							return [];
						}else{
							$duplicate_found = true;
						}
					}
					if(!$duplicate_found) {
						$monster_not_made = false;
						$reward['type_id'] = $type_id;
					}
				}
			}elseif($reward['type'] == 'Gems') {
				//check if we already have gems at equal or higher value
				foreach($rewards_currently as $index=>$reward_currently) { 
					if($reward_currently['type'] == 'Gems') {
						if($reward['amount'] <= $reward_currently['amount']) {
							return [];
						}else{
							unset($rewards_currently[$index]);
						}
					}
				}
			}elseif($reward['type'] == 'Gold') {
				//check if we already have gold at equal or higher value
				foreach($rewards_currently as $index=>$reward_currently) { 
					if($reward_currently['type'] == 'Gold') {
						if($reward['amount'] <= $reward_currently['amount']) {
							return [];
						}else{
							unset($rewards_currently[$index]);
						}
					}
				}
			}
			$reward['rarity'] = $rarity;
			return $reward;
		}else{
			return [];
		}
	}
	*/
	/*
	public function choose_reward($available_reward_id) {
		$user_id = $this->Auth->user('id');
		$available_reward = $this->AvailableReward->findById($available_reward_id);
		if (empty($available_reward['AvailableReward']['id'])) {
			throw new NotFoundException(__('Invalid reward'));
		}elseif($available_reward['AvailableReward']['user_id'] != $user_id) {
			throw new ForbiddenException(__('Not Your Reward'));
		}elseif($available_reward['AvailableReward']['chosen'] == 1) {
			throw new ForbiddenException(__('Reward Already Chosen'));
		}elseif($available_reward['GauntletRun']['number_of_reward_picks'] <= $available_reward['GauntletRun']['number_of_rewards_chosen']) {
			throw new ForbiddenException(__('You have chosen all of your rewards already.'));
		}
		$this->GauntletRun->id = $available_reward['AvailableReward']['gauntlet_run_id'];
		$available_reward['GauntletRun']['number_of_rewards_chosen']++;
		$this->GauntletRun->saveField('number_of_rewards_chosen', $available_reward['GauntletRun']['number_of_rewards_chosen']);
		
		if($available_reward['AvailableReward']['type'] == 'Skill') {
			$this->UserSkill->create();
			$user_skill['UserSkill'] = [
				'id' => null,
				'user_id' => $user_id,
				'skill_id' => $available_reward['AvailableReward']['skill_id']
			];
			$this->UserSkill->save($user_skill);
			$this->Flash->success(__('You have unlocked the Skill: '.$available_reward['Skill']['name'].'!'));
		}elseif($available_reward['AvailableReward']['type'] == 'Ultimate') {
			$this->UserUltimate->create();
			$user_ultimate['UserUltimate'] = [
				'id' => null,
				'user_id' => $user_id,
				'ultimate_id' => $available_reward['AvailableReward']['ultimate_id']
			];
			$this->UserUltimate->save($user_ultimate);
			$this->Flash->success(__('You have unlocked the Ultimate: '.$available_reward['Ultimate']['name'].'!'));
		}elseif($available_reward['AvailableReward']['type'] == 'Augment') {
			$this->UserAugment->create();
			$user_augment['UserAugment'] = [
				'id' => null,
				'user_id' => $user_id,
				'augment_id' => $available_reward['AvailableReward']['augment_id']
			];
			$this->UserAugment->save($user_augment);
			$this->Flash->success(__('You have unlocked: '.$available_reward['Augment']['name'].'!'));
		}elseif($available_reward['AvailableReward']['type'] == 'Dual Type Monster') {
			//find average elo
			$monsters = $this->Monster->find('all', [
				'conditions' => [
					'Monster.user_id' => $user_id,
					'Monster.total_battles >' => 20
				]
			]);
			if(empty($monsters)) {
				$elo_rating = 1900;
			}else{
				$total_elo_rating = 0;
				foreach($monsters as $monster) {
					$total_elo_rating += $monster['Monster']['elo_rating'];
				}
				$elo_rating = $total_elo_rating / count($monsters);
			}
			
			$this->Monster->createMonster($user_id, $available_reward['AvailableReward']['type_id'], $available_reward['AvailableReward']['secondary_type_id'], $elo_rating);
			$this->Flash->success(__('You gained a new '.$available_reward['Type']['name'].' and '.$available_reward['SecondaryType']['name'].' Dual Type Monster!'));
		}elseif($available_reward['AvailableReward']['type'] == 'Monster') {
			//find average elo
			$monsters = $this->Monster->find('all', [
				'conditions' => [
					'Monster.user_id' => $user_id,
					'Monster.total_battles >' => 20
				]
			]);
			if(empty($monsters)) {
				$elo_rating = 1900;
			}else{
				$total_elo_rating = 0;
				foreach($monsters as $monster) {
					$total_elo_rating += $monster['Monster']['elo_rating'];
				}
				$elo_rating = $total_elo_rating / count($monsters);
			}
			$this->Monster->createMonster($user_id, $available_reward['AvailableReward']['type_id'], 0, $elo_rating);
			$this->Flash->success(__('You gained a new '.$available_reward['Type']['name'].' Type Monster!'));
		}elseif($available_reward['AvailableReward']['type'] == 'Gems') {
			$user = $this->User->findById($user_id);
			$this->User->id = $user_id;
			$this->User->saveField('gems', $user['User']['gems'] + $available_reward['AvailableReward']['amount']);
			$this->Flash->success(__('You gained '.$available_reward['AvailableReward']['amount'].' Gem'.($available_reward['AvailableReward']['amount'] == 1 ? '' : 's').'!'));
		}elseif($available_reward['AvailableReward']['type'] == 'Gold') {
			$user = $this->User->findById($user_id);
			$this->User->id = $user_id;
			$this->User->saveField('gold', $user['User']['gold'] + $available_reward['AvailableReward']['amount']);
			$this->Flash->success(__('You gained '.$available_reward['AvailableReward']['amount'].' Gold!'));
		}
		$this->AvailableReward->id = $available_reward['AvailableReward']['id'];
		$this->AvailableReward->saveField('chosen', 1);
		if($available_reward['GauntletRun']['number_of_rewards_chosen'] >= $available_reward['GauntletRun']['number_of_reward_picks']) {
			$this->redirect('/new/');
		}else{
			$this->redirect(['action' => 'view_results', $available_reward['AvailableReward']['gauntlet_run_id']]);
		}
		
	}*/
	
}