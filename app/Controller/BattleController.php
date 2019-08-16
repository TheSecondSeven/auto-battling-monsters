<?php
App::uses('AppController', 'Controller');
/**
 * Monsters Controller
 *
 * @property Monster $Monster
 * @property PaginatorComponent $Paginator
 */
class BattleController extends AppController {
/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session', 'Flash');
	
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow([
			'view'
		]);
	}
/**
 * Uses
 *
 * @var array
 */
	public $uses = [
		'Monster',
		'Skill',
		'SkillEffect',
		'GauntletRun',
		'GauntletRunBattle',
		'Status'
	];
	
	public function practice($monster_id) {
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
		}
		$options['order'] = [
			'rand()'
		];
		//find opponent
		unset($options['conditions']);
		$opponent = null;
		$elo_threshold = 1;
		while(empty($opponent['Monster']['id'])) {
			$options['conditions'] = [
				'NOT' => [
					'Monster.id' => [$monster['Monster']['id']]
				],
				'Monster.skill_1_id != 0',
				'Monster.skill_2_id != 0',
				'Monster.skill_3_id != 0',
				'Monster.skill_4_id != 0',
				'Monster.ultimate_id != 0'
			];
			if($elo_threshold < 10) {
				$elo_threshold_amount = $elo_threshold * 50;
				$options['conditions']['Monster.elo_rating >='] = $monster['Monster']['elo_rating'] - $elo_threshold_amount;
				$options['conditions']['Monster.elo_rating <='] = $monster['Monster']['elo_rating'] + $elo_threshold_amount;
			}
			$opponent = $this->Monster->find('first', $options);
			$elo_threshold++;
		}
		$result = $this->Combat->twoTeamCombat($monster, $opponent);
		$this->set('battlesJSON',json_encode([$result],JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP));
		$this->set('statuses',$this->Status->find('all'));
		$this->render('view');
			
	}

	public function winRates($simulations = 1000, $specific_id = null) {
		set_time_limit(0);
		$this->action_log = [];
		$dont_find_monsters = [];
		$conditions = [];
		if($specific_id) {
			$conditions = [
				'Monster.id' => $specific_id,
				'Monster.skill_1_id != 0',
				'Monster.skill_2_id != 0',
				'Monster.skill_3_id != 0',
				'Monster.skill_4_id != 0',
				'Monster.ultimate_id != 0'
			];
		}
		$monsters = $this->Monster->find('all',[
			'conditions' => $conditions,
			'recursive' => 1,
			'contain' => [
				'Type',
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
				],
				'User'
			]
		]);
		$total = [];
		foreach($monsters as $monster_index => $monster) {
			$dont_find_monsters[] = $monster['Monster']['id'];
			$opponents = $this->Monster->find('all', [
				'conditions' => [
					'NOT' => [
						'Monster.id' => $dont_find_monsters
					],
					'Monster.skill_1_id != 0',
					'Monster.skill_2_id != 0',
					'Monster.skill_3_id != 0',
					'Monster.skill_4_id != 0',
					'Monster.ultimate_id != 0'
				],
				'recursive' => 1,
				'contain' => [
					'Type',
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
					],
					'User'
				]
			]);
			foreach($opponents as $opponent_index => $opponent) {
				
				if(empty($total[$monster['Monster']['id']])) {
					$total[$monster['Monster']['id']] = [
						'id' => $monster['Monster']['id'],
						'name' => $monster['Monster']['name'],
						'elo_rating' => $monster['Monster']['elo_rating'],
						'elo_change' => 0,
						'ultimate' => $monster['Ultimate']['name'],
						'wins' => 0,
						'losses' => 0,
						'ties' => 0,
						'total_e' => 0,
						'total_s' => 0
					];
				}
				if(empty($total[$opponent['Monster']['id']])) {
					$total[$opponent['Monster']['id']] = [
						'id' => $opponent['Monster']['id'],
						'name' => $opponent['Monster']['name'],
						'elo_rating' => $opponent['Monster']['elo_rating'],
						'elo_change' => 0,
						'ultimate' => $opponent['Ultimate']['name'],
						'wins' => 0,
						'losses' => 0,
						'ties' => 0,
						'total_e' => 0,
						'total_s' => 0
					];
				}
				$wins = 0;
				$losses = 0;
				$ties = 0;
				for($i=0;$i<$simulations;$i++) {
					$monster['Monster']['elo_rating'] = $total[$monster['Monster']['id']]['elo_rating'];
					$opponent['Monster']['elo_rating'] = $total[$opponent['Monster']['id']]['elo_rating'];
					
					$result = $this->Combat->twoTeamCombat($monster, $opponent);
					$elo_change = $this->eloUpdate($monster, $opponent, $result['winning_id']);
					$total[$monster['Monster']['id']]['elo_change'] += $elo_change;
					$total[$monster['Monster']['id']]['elo_rating'] += $elo_change;
					
					$this->Monster->id = $monster['Monster']['id'];
					$this->Monster->saveField('elo_rating', $total[$monster['Monster']['id']]['elo_rating']);
					
					$total[$opponent['Monster']['id']]['elo_change'] -= $elo_change;
					$total[$opponent['Monster']['id']]['elo_rating'] -= $elo_change;
					
					$this->Monster->id = $opponent['Monster']['id'];
					$this->Monster->saveField('elo_rating', $total[$opponent['Monster']['id']]['elo_rating']);
					
					
					if($result['winning_id'] <= 0) {
						$ties++;
					}elseif($result['winning_id'] == 1) {
						$wins++;
					}else{
						$losses++;
					}
				}
				$total[$monster['Monster']['id']]['wins'] += $wins;
				$total[$monster['Monster']['id']]['losses'] += $losses;
				$total[$monster['Monster']['id']]['ties'] += $ties;
				
				$total[$opponent['Monster']['id']]['wins'] += $losses;
				$total[$opponent['Monster']['id']]['losses'] += $wins;
				$total[$opponent['Monster']['id']]['ties'] += $ties;
				//echo $monster['Monster']['name'].' vs '.$opponent['Monster']['name'].' '.($wins / ($simulations / 100)).'% Wins '.($losses / ($simulations / 100)).'% Losses '.($ties / ($simulations / 100)).'% Ties<br>';
			}
			
		}
		usort($total, array(&$this, 'sortByELORating'));
		foreach($total as $monster) {
			//$total = $monster['wins'] + $monster['losses'] +$monster['ties'];
			//echo 'Name: '.$monster['name'].'<br>Ultimate: '.$monster['ultimate'].'<br>Win %: '.round($monster['wins'] / $total * 100,2).'<br>Loss %: '.round($monster['losses'] / $total * 100,2).'<br>Tie %: '.round($monster['ties'] / $total * 100,2).'<br><br>'; 
		}
		$this->set('total', $total);
	}

	function sortByWins($a, $b) {
		return $b['wins'] - $a['wins'];
	}
	function sortByELOChange($a, $b) {
		return $b['elo_change'] - $a['elo_change'];
	}
	function sortByELORating($a, $b) {
		return $b['elo_rating'] - $a['elo_rating'];
	}
	
	
	
	
	
/**
 * battle method
 *
 * @throws NotFoundException
 * @param int $first_monster_id
 * @param int $second_monster_id
 * @return void
 */
	public function view($first_monster_id = null, $second_monster_id = null, $third_monster_id = null, $fourth_monster_id = null, $sampling = false) {
		if (!$this->Monster->exists($first_monster_id)) {
			throw new NotFoundException(__('Invalid First Monster'));
		}
		$options = [
			'conditions' => [
				'Monster.' . $this->Monster->primaryKey => $first_monster_id
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
				],
				'User'
			]
		];
		$firstMonster = $this->Monster->find('first', $options);
		
		
		if (!$this->Monster->exists($second_monster_id)) {
			throw new NotFoundException(__('Invalid Second Monster'));
		}
		$options['conditions']['Monster.' . $this->Monster->primaryKey] = $second_monster_id;
		$secondMonster = $this->Monster->find('first', $options);
		
		$result = $this->Combat->twoTeamCombat($firstMonster, $secondMonster);
		$this->eloUpdate($firstMonster, $secondMonster, $result['winning_id']);
		$this->set('battlesJSON',json_encode([$result],JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP));
		$this->set('statuses',$this->Status->find('all'));
	}
	
	private function eloUpdate($monster_1, $monster_2, $winning_id) {
		
		//ELO System
		$q1 = pow(10, ($monster_1['Monster']['elo_rating']) / 400);
		$q2 = pow(10, ($monster_2['Monster']['elo_rating']) / 400);
		
		$e1 = $q1 / ($q1 + $q2);
		$e2 = $q2 / ($q1 + $q2);
		
		if($winning_id == 1) {
			$s1 = 1;
			$s2 = 0;
		}elseif($winning_id == 2) {
			$s2 = 1;
			$s1 = 0;
		}else{
			$s1 = 0.5;
			$s2 = 0.5;
		}
		//$firstMonster
		$ratingChange = round(ELO_CONSTANT * ($s1 - $e1));
		$this->Monster->id = $monster_1['Monster']['id'];
		$this->Monster->saveField('elo_rating', $monster_1['Monster']['elo_rating'] + $ratingChange);
		
		//$secondMonster
		$secondRatingChange = round(ELO_CONSTANT * ($s2 - $e2));
		$this->Monster->id = $monster_2['Monster']['id'];
		$this->Monster->saveField('elo_rating', $monster_2['Monster']['elo_rating'] + $secondRatingChange);
		return $ratingChange;
	}
}