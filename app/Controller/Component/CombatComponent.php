<?php
App::uses('Component', 'Controller');

class CombatComponent extends Component {
	
	private $originalMonsters = [];
	private $monsters = [];
	private $action_log = [];
	private $monstersToRemove = [];
	private $environment = [
		'statuses' => []
	];
    public function initialize(Controller $controller) {
	    
    }
    

	private function getMonsterID() {
		$IDNotFound = true;
		while($IDNotFound == true) {
			$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$ID = '';
		    for ($i = 0; $i < 10; $i++) {
		        $ID .= $characters[rand(0, strlen($characters) - 1)];
		    }
			if(empty($this->monsters[$ID])) {
				$IDNotFound = false;
			}
		}
		return $ID;
	}
	
	public function twoTeamCombat($monster, $opponent) {
		
		$this->monsters = [];
		$monster['id'] = $this->getMonsterID();
		$monster['Monster']['team'] = 1;
		$this->monsters[$monster['id']] = $monster;
		$opponent['id'] = $this->getMonsterID();
		$opponent['Monster']['team'] = 2;
		$this->monsters[$opponent['id']] = $opponent;
		$this->originalMonsters = $this->monsters;
		
		foreach($this->monsters as $monster) {
			$this->setupMonster($monster, 0);
		}
		$this->setupTeams();
		$time = 0;
		$this->action_log = [];
		$battle_over = false;
		while($time <= 60000 && $battle_over == false) {
			$this->processStatuses($time, $this->monsters);
			foreach($this->monsters as $monster) {
				if($this->monsters[$monster['id']]['Monster']['next_action_time'] <= $time && $this->monsters[$monster['id']]['Monster']['current_health'] > 0) {
					$this->nextAction($time, $monster);
				}
			}
			
			if(!empty($this->action_log[$time])) {
				foreach($this->monsters as $monster) {
					$this->action_log[$time]['state']['monster-'.$this->monsters[$monster['id']]['id']] = [
						'name' => $this->monsters[$monster['id']]['Monster']['name'],
						'max_health' => $this->monsters[$monster['id']]['Monster']['max_health'],
						'current_health' => $this->monsters[$monster['id']]['Monster']['current_health'],
						'team' => $this->monsters[$monster['id']]['Monster']['team'],
						'statuses' => $this->monsters[$monster['id']]['Monster']['statuses'],
						'debuffs' => $this->monsters[$monster['id']]['Monster']['debuffs'],
						'buffs' => $this->monsters[$monster['id']]['Monster']['buffs']
					];
				}
				$this->action_log[$time]['environment'] = $this->environment;
				$this->action_log[$time]['time'] = $time;
			}
			foreach($this->monsters as $monster) {
				$hasEnemies = false;
				foreach($this->monsters[$monster['id']]['enemies'] as $enemy) {
					if($this->monsters[$enemy['id']]['Monster']['current_health'] > 0)
						$hasEnemies = true;
				}
				if(!$hasEnemies) {
					$battle_over = true;
					break;
				}
			}
			$monsterRemoved = false;
			foreach($this->monstersToRemove as $monsterID) {
				if(!empty($this->monsters[$monsterID])) {
					unset($this->monsters[$monsterID]);
					$monsterRemoved = true;
				}
			}
			if($monsterRemoved) {
				$this->setupTeams();
			}
			$this->monstersToRemove = [];
			$time += 50;
		}
		$winning_id = 0;
	
		foreach($this->monsters as $monster) {
			if($this->monsters[$monster['id']]['Monster']['current_health'] > 0) {
				if($winning_id == 0 || $winning_id == $this->monsters[$monster['id']]['Monster']['team']) {
					$winning_id = $this->monsters[$monster['id']]['Monster']['team'];
				}else{
					$winning_id = -1;
				}
			}
		}
		$statusModel = ClassRegistry::init('Status');
		
		$status_list = $statusModel->find('all');
		
		$status_types = [];
		$buff_types = [];
		$debuff_types = [];
		
		foreach($status_list as $status) {
			if($status['Status']['type'] == 'Status') {
				$status_types[] = [
					'id' => $status['Status']['class'],
					'name' => $status['Status']['name']
				];
			}
			if($status['Status']['type'] == 'Buff') {
				$buff_types[] = [
					'id' => $status['Status']['class'],
					'name' => $status['Status']['name']
				];
			}
			if($status['Status']['type'] == 'Debuff') {
				$debuff_types[] = [
					'id' => $status['Status']['class'],
					'name' => $status['Status']['name']
				];
			}
		}
		
		$statuses = [];
		$buffs = [];
		$debuffs = [];
		foreach($this->action_log as $time_log) {
			if(!empty($time_log['state'])) {
				foreach($time_log['state'] as $monster_state) {
					foreach($monster_state['statuses'] as $status_key => $status) {
						foreach($status_types as $a_status) {
							if($a_status['id'] == $status_key) {
								$statuses[$a_status['id']] = $a_status['name'];
								break;
							}
						}
					}
					foreach($monster_state['buffs'] as $status_key => $status) {
						foreach($buff_types as $a_buff) {
							if($a_buff['id'] == $status_key) {
								$buffs[$a_buff['id']] = $a_buff['name'];
								break;
							}
						}
					}
					foreach($monster_state['debuffs'] as $status_key => $status) {
						foreach($debuff_types as $a_debuff) {
							if($a_debuff['id'] == $status_key) {
								$debuffs[$a_debuff['id']] = $a_debuff['name'];
								break;
							}
						}
					}
				}
			}
		}
		return [
			'action_log' => $this->action_log,
			'winning_id' => $winning_id,
			'used_statuses' => $statuses,
			'used_buffs' => $buffs,
			'used_debuffs' => $debuffs
		];
	}
	
	private function augmentSlot($monster, $slot) {
		if(!empty($this->monsters[$monster['id']][$slot]['id']) && !empty($this->monsters[$monster['id']]['Augment'.$slot]['id']) && $this->monsters[$monster['id']][$slot]['type_id'] == $this->monsters[$monster['id']]['Augment'.$slot]['amount_1']) {
			if($this->monsters[$monster['id']]['Augment'.$slot]['type'] == 'Damage') {
				foreach($this->monsters[$monster['id']][$slot]['SkillEffect'] as $skill_effect_index => $skill_effect) {
					if(in_array($skill_effect['effect'], ['Physical Damage','Magical Damage','Leech','True Damage','Poison'])) {
						$this->monsters[$monster['id']][$slot]['SkillEffect'][$skill_effect_index]['amount_min'] = round($this->monsters[$monster['id']][$slot]['SkillEffect'][$skill_effect_index]['amount_min'] * (1 + $this->monsters[$monster['id']]['Augment'.$slot]['amount_2'] / 100));
						$this->monsters[$monster['id']][$slot]['SkillEffect'][$skill_effect_index]['amount_max'] = round($this->monsters[$monster['id']][$slot]['SkillEffect'][$skill_effect_index]['amount_max'] * (1 + $this->monsters[$monster['id']]['Augment'.$slot]['amount_2'] / 100));
					}
					foreach($this->monsters[$monster['id']][$slot]['SkillEffect'][$skill_effect_index]['SecondarySkillEffect'] as $secondary_skill_effect_index => $secondary_skill_effect) {
						if(in_array($secondary_skill_effect['effect'], ['Physical Damage','Magical Damage','True Damage','Leech','Poison'])) {
							$this->monsters[$monster['id']][$slot]['SkillEffect'][$skill_effect_index]['SecondarySkillEffect'][$secondary_skill_effect_index]['amount_min'] = round($this->monsters[$monster['id']][$slot]['SkillEffect'][$skill_effect_index]['SecondarySkillEffect'][$secondary_skill_effect_index]['amount_min'] * (1 + $this->monsters[$monster['id']]['Augment'.$slot]['amount_2'] / 100));
							$this->monsters[$monster['id']][$slot]['SkillEffect'][$skill_effect_index]['SecondarySkillEffect'][$secondary_skill_effect_index]['amount_max'] = round($this->monsters[$monster['id']][$slot]['SkillEffect'][$skill_effect_index]['SecondarySkillEffect'][$secondary_skill_effect_index]['amount_max'] * (1 + $this->monsters[$monster['id']]['Augment'.$slot]['amount_2'] / 100));
						}
					}
				}
			}elseif($this->monsters[$monster['id']]['Augment'.$slot]['type'] == 'Healing') {
				foreach($this->monsters[$monster['id']][$slot]['SkillEffect'] as $skill_effect_index => $skill_effect) {
					if(in_array($skill_effect['effect'], ['Heal','Healing Over Time'])) {
						$this->monsters[$monster['id']][$slot]['SkillEffect'][$skill_effect_index]['amount_min'] = round($this->monsters[$monster['id']][$slot]['SkillEffect'][$skill_effect_index]['amount_min'] * (1 + $this->monsters[$monster['id']]['Augment'.$slot]['amount_2'] / 100));
						$this->monsters[$monster['id']][$slot]['SkillEffect'][$skill_effect_index]['amount_max'] = round($this->monsters[$monster['id']][$slot]['SkillEffect'][$skill_effect_index]['amount_max'] * (1 + $this->monsters[$monster['id']]['Augment'.$slot]['amount_2'] / 100));
					}
					foreach($this->monsters[$monster['id']][$slot]['SkillEffect'][$skill_effect_index]['SecondarySkillEffect'] as $secondary_skill_effect_index => $secondary_skill_effect) {
						if(in_array($secondary_skill_effect['effect'], ['Heal','Healing Over Time'])) {
							$this->monsters[$monster['id']][$slot]['SkillEffect'][$skill_effect_index]['SecondarySkillEffect'][$secondary_skill_effect_index]['amount_min'] = round($this->monsters[$monster['id']][$slot]['SkillEffect'][$skill_effect_index]['SecondarySkillEffect'][$secondary_skill_effect_index]['amount_min'] * (1 + $this->monsters[$monster['id']]['Augment'.$slot]['amount_2'] / 100));
							$this->monsters[$monster['id']][$slot]['SkillEffect'][$skill_effect_index]['SecondarySkillEffect'][$secondary_skill_effect_index]['amount_max'] = round($this->monsters[$monster['id']][$slot]['SkillEffect'][$skill_effect_index]['SecondarySkillEffect'][$secondary_skill_effect_index]['amount_max'] * (1 + $this->monsters[$monster['id']]['Augment'.$slot]['amount_2'] / 100));
						}
					}
				}
			}elseif($this->monsters[$monster['id']]['Augment'.$slot]['type'] == 'Chance To Cast Again') {
				$this->monsters[$monster['id']][$slot]['cast_again_chance'] = $this->monsters[$monster['id']]['Augment'.$slot]['amount_2'];
			}
		}
	}
	
	
	private function applyRunes($monster) {
		for($i=1; $i <= 3; $i++) {
			if(!empty($this->monsters[$monster['id']]['Rune'.$i]['id'])) {
				$rune = $this->monsters[$monster['id']]['Rune'.$i];
				if($rune['damage_level'] > 0) {
					$increase_amount = 1 + RUNE_DAMAGE_INCREASE * $rune['damage_level'] / 100;
					for($j=1; $j<=4; $j++) {
						if($this->monsters[$monster['id']]['Skill'.$j]['type_id'] == $rune['type_id']) {
							foreach($this->monsters[$monster['id']]['Skill'.$j]['SkillEffect'] as $skill_effect_index => $skill_effect) {
								if(in_array($skill_effect['effect'], ['Physical Damage','Magical Damage','Leech','True Damage','Poison'])) {
									$this->monsters[$monster['id']]['Skill'.$j]['SkillEffect'][$skill_effect_index]['amount_min'] = round($this->monsters[$monster['id']]['Skill'.$j]['SkillEffect'][$skill_effect_index]['amount_min'] * $increase_amount);
									$this->monsters[$monster['id']]['Skill'.$j]['SkillEffect'][$skill_effect_index]['amount_max'] = round($this->monsters[$monster['id']]['Skill'.$j]['SkillEffect'][$skill_effect_index]['amount_max'] * $increase_amount);
								}
								foreach($this->monsters[$monster['id']]['Skill'.$j]['SkillEffect'][$skill_effect_index]['SecondarySkillEffect'] as $secondary_skill_effect_index => $secondary_skill_effect) {
									if(in_array($secondary_skill_effect['effect'], ['Physical Damage','Magical Damage','True Damage','Leech','Poison'])) {
										$this->monsters[$monster['id']]['Skill'.$j]['SkillEffect'][$skill_effect_index]['SecondarySkillEffect'][$secondary_skill_effect_index]['amount_min'] = round($this->monsters[$monster['id']]['Skill'.$j]['SkillEffect'][$skill_effect_index]['SecondarySkillEffect'][$secondary_skill_effect_index]['amount_min'] * $increase_amount);
										$this->monsters[$monster['id']]['Skill'.$j]['SkillEffect'][$skill_effect_index]['SecondarySkillEffect'][$secondary_skill_effect_index]['amount_max'] = round($this->monsters[$monster['id']]['Skill'.$j]['SkillEffect'][$skill_effect_index]['SecondarySkillEffect'][$secondary_skill_effect_index]['amount_max'] * $increase_amount);
									}
								}
							}
						}
					}
				}
				if($rune['healing_level'] > 0) {
					$increase_amount = 1 + RUNE_HEALING_INCREASE * $rune['healing_level'] / 100;
					for($j=1; $j<=4; $j++) {
						if($this->monsters[$monster['id']]['Skill'.$j]['type_id'] == $rune['type_id']) {
							foreach($this->monsters[$monster['id']]['Skill'.$j]['SkillEffect'] as $skill_effect_index => $skill_effect) {
								if(in_array($skill_effect['effect'], ['Heal','Healing Over Time'])) {
									$this->monsters[$monster['id']]['Skill'.$j]['SkillEffect'][$skill_effect_index]['amount_min'] = round($this->monsters[$monster['id']]['Skill'.$j]['SkillEffect'][$skill_effect_index]['amount_min'] * $increase_amount);
									$this->monsters[$monster['id']]['Skill'.$j]['SkillEffect'][$skill_effect_index]['amount_max'] = round($this->monsters[$monster['id']]['Skill'.$j]['SkillEffect'][$skill_effect_index]['amount_max'] * $increase_amount);
								}
								foreach($this->monsters[$monster['id']]['Skill'.$j]['SkillEffect'][$skill_effect_index]['SecondarySkillEffect'] as $secondary_skill_effect_index => $secondary_skill_effect) {
									if(in_array($secondary_skill_effect['effect'], ['Heal','Healing Over Time'])) {
										$this->monsters[$monster['id']]['Skill'.$j]['SkillEffect'][$skill_effect_index]['SecondarySkillEffect'][$secondary_skill_effect_index]['amount_min'] = round($this->monsters[$monster['id']]['Skill'.$j]['SkillEffect'][$skill_effect_index]['SecondarySkillEffect'][$secondary_skill_effect_index]['amount_min'] * $increase_amount);
										$this->monsters[$monster['id']]['Skill'.$j]['SkillEffect'][$skill_effect_index]['SecondarySkillEffect'][$secondary_skill_effect_index]['amount_max'] = round($this->monsters[$monster['id']]['Skill'.$j]['SkillEffect'][$skill_effect_index]['SecondarySkillEffect'][$secondary_skill_effect_index]['amount_max'] * $increase_amount);
									}
								}
							}
						}
					}
				}
				if($rune['critical_chance_level'] > 0) {
					$increase_amount = RUNE_CRITICAL_CHANCE_INCREASE * $rune['critical_chance_level'] / 100;
					for($j=1; $j<=4; $j++) {
						if($this->monsters[$monster['id']]['Skill'.$j]['type_id'] == $rune['type_id']) {
							if(empty($this->monsters[$monster['id']]['Skill'.$j]['critical_chance_increase'])) {
								$this->monsters[$monster['id']]['Skill'.$j]['critical_chance_increase'] = 0;
							}
							$this->monsters[$monster['id']]['Skill'.$j]['critical_chance_increase'] += $increase_amount;
						}
					}
				}
				if($rune['cast_again_level'] > 0) {
					$increase_amount = RUNE_CAST_AGAIN_INCREASE * $rune['cast_again_level'] / 100;
					for($j=1; $j<=4; $j++) {
						if($this->monsters[$monster['id']]['Skill'.$j]['type_id'] == $rune['type_id']) {
							if(empty($this->monsters[$monster['id']]['Skill'.$j]['cast_again_chance'])) {
								$this->monsters[$monster['id']]['Skill'.$j]['cast_again_chance'] = 0;
							}
							$this->monsters[$monster['id']]['Skill'.$j]['cast_again_chance'] += $increase_amount;
						}
					}
				}
				if($rune['casting_speed_level'] > 0) {
					$increase_amount = RUNE_CASTING_SPEED_INCREASE * $rune['casting_speed_level'] / 100;
					for($j=1; $j<=4; $j++) {
						if($this->monsters[$monster['id']]['Skill'.$j]['type_id'] == $rune['type_id']) {
							if(empty($this->monsters[$monster['id']]['Skill'.$j]['casting_speed_increase'])) {
								$this->monsters[$monster['id']]['Skill'.$j]['casting_speed_increase'] = 0;
							}
							$this->monsters[$monster['id']]['Skill'.$j]['casting_speed_increase'] += $increase_amount;
						}
					}
				}
				if($rune['health_level'] > 0) {
					$increase_amount = 1 + RUNE_HEALTH_INCREASE * $rune['health_level'] / 100;
					$this->monsters[$monster['id']]['Monster']['max_health'] = round($this->monsters[$monster['id']]['Monster']['max_health'] * $increase_amount);
					$this->monsters[$monster['id']]['Monster']['current_health'] = round($this->monsters[$monster['id']]['Monster']['current_health'] * $increase_amount);
				}
			}
		}
	}
	
	
	private function setupMonster($monster, $time) {
		
		$this->monsters[$monster['id']]['Stats'] = $this->createStats($this->monsters[$monster['id']]);
		$this->monsters[$monster['id']]['Monster']['statuses'] = [];
		$this->monsters[$monster['id']]['Monster']['debuffs'] = [];
		$this->monsters[$monster['id']]['Monster']['buffs'] = [];
		if(empty($this->monsters[$monster['id']]['Monster']['max_health'])) {
			$this->monsters[$monster['id']]['Monster']['max_health'] = $this->monsters[$monster['id']]['Stats']['health'];
			$this->monsters[$monster['id']]['Monster']['current_health'] = $this->monsters[$monster['id']]['Stats']['health'];
		}
		$this->monsters[$monster['id']]['Monster']['next_action_time'] = $time;
		$this->monsters[$monster['id']]['Monster']['next_use_skill'] = null;
		$this->monsters[$monster['id']]['Monster']['next_action_skill'] = 0;
		if(empty($this->monsters[$monster['id']]['cc_count'])) {
			$this->monsters[$monster['id']]['cc_count'] = 0;
		}
		if(empty($this->monsters[$monster['id']]['skills'])) {
			$this->monsters[$monster['id']]['skills'] = [];
			/*
			$this->augmentSlot($monster, 'Skill1');
			$this->augmentSlot($monster, 'Skill2');
			$this->augmentSlot($monster, 'Skill3');
			$this->augmentSlot($monster, 'Skill4');
			$this->augmentSlot($monster, 'Ultimate');
			*/
			$this->applyRunes($monster);
			if(!empty($this->monsters[$monster['id']]['Skill1']['id'])) {
				$this->monsters[$monster['id']]['skills'] = [
					$this->monsters[$monster['id']]['Skill1'],
					$this->monsters[$monster['id']]['Skill2'],
					$this->monsters[$monster['id']]['Skill3'],
					$this->monsters[$monster['id']]['Skill4']
				];
			}
			if(!$this->monsters[$monster['id']]['Ultimate']['passive']) {
				$this->monsters[$monster['id']]['Ultimate']['ultimate'] = 1;
				$this->monsters[$monster['id']]['skills'][] = $this->monsters[$monster['id']]['Ultimate'];
			}
			$this->setupPassiveUltimate($monster);
		}
	}
	
	private function setupPassiveUltimate($monster) {
		if($this->monsters[$monster['id']]['Ultimate']['id'] == 11) {
			$this->monsters[$monster['id']]['Monster']['statuses']['discharge']['stacks'] = 0;
		}
		if($this->monsters[$monster['id']]['Ultimate']['id'] == 13) {
			$this->monsters[$monster['id']]['Monster']['statuses']['phoenix']['stacks'] = 1;
		}
		if($this->monsters[$monster['id']]['Ultimate']['id'] == 15) {
			$this->monsters[$monster['id']]['Monster']['statuses']['white-belt'] = true;
		}
		if($this->monsters[$monster['id']]['Ultimate']['id'] == 19) {
			$this->monsters[$monster['id']]['Monster']['statuses']['living-flesh'] = true;
		}
	}
	
	private function setupTeams() {
		foreach($this->monsters as $monster) {
			$this->monsters[$monster['id']]['allies'] = [];
			$this->monsters[$monster['id']]['enemies'] = [];
			foreach($this->monsters as $other_monster) {
				if($this->monsters[$other_monster['id']]['id'] != $this->monsters[$monster['id']]['id']) {
					if($this->monsters[$other_monster['id']]['Monster']['team'] == $this->monsters[$monster['id']]['Monster']['team']) {
						$this->monsters[$monster['id']]['allies'][] = $other_monster;
					}else{
						$this->monsters[$monster['id']]['enemies'][] = $other_monster;
					}
				}
			}
		}
	}
	
	private function addActionMessage(&$action_message, $message_type, $message_text) {
		$action_message[] = [
			'type' => $message_type,
			'text' => $message_text
		];
	}
	
	private function addHealthChangeLog($time, $monster, $type, $amount) {
		$this->action_log[$time]['healthChange']['monster-'.$this->monsters[$monster['id']]['id']][] = [
			'type' => $type,
			'amount' => $amount
		];
	}
	
	private function processStatuses($time, $monsters) {
		foreach($monsters as $monster) {
			//check for enemies
			$hasEnemies = false;
			foreach($this->monsters[$monster['id']]['enemies'] as $enemy) {
				if($this->monsters[$enemy['id']]['Monster']['current_health'] > 0)
					$hasEnemies = true;
			}
			//keep the battle going after all enemies die
			//$hasEnemies = true;
			if($this->monsters[$monster['id']]['Monster']['current_health'] > 0 && $hasEnemies) {
				$action_message = [];
				if(!empty($this->monsters[$monster['id']]['Monster']['buffs']['healing-over-time'])) {
					$this->processStatusHealingOverTime($time, $action_message, $monster);
				}
				if(!empty($this->monsters[$monster['id']]['Monster']['debuffs']['poisoned'])) {
					$this->processStatusPoisoned($time, $action_message, $monster);
				}
				if(!empty($this->monsters[$monster['id']]['Monster']['debuffs']['stunned'])) {
					$this->processStatusStunned($time, $action_message, $monster);
				}
				if(!empty($this->monsters[$monster['id']]['Monster']['debuffs']['asleep'])) {
					$this->processStatusAsleep($time, $action_message, $monster);
				}
				if(!empty($this->monsters[$monster['id']]['Monster']['debuffs']['frozen'])) {
					$this->processStatusFrozen($time, $action_message, $monster, false);
				}
				if(!empty($this->monsters[$monster['id']]['Monster']['debuffs']['confused'])) {
					$this->processStatusConfused($time, $action_message, $monster);
				}
				if(!empty($this->monsters[$monster['id']]['Monster']['debuffs']['burned'])) {
					$this->processStatusBurned($time, $action_message, $monster);
				}
				$this->processStatusWetness($time, $action_message, $monster);
				
				if(!empty($this->monsters[$monster['id']]['Monster']['statuses']['phoenix-reborn'])) {
					$this->processStatusPhoenixReborn($time, $action_message, $monster);
				}
				if(!empty($this->monsters[$monster['id']]['Monster']['statuses']['rotting'])) {
					$this->processStatusRotting($time, $action_message, $monster);
				}
				if(!empty($action_message))
					$this->action_log[$time]['messages']['monster-'.$this->monsters[$monster['id']]['id']][] = $action_message;
			}
			foreach($this->environment['statuses'] as $index => $environment_status) {
				if($environment_status['ends'] != 0 && $environment_status['ends'] <= $time) {
					unset($this->environment['statuses'][$index]);
				}
			}
		}
	}
	
	private function tickStatuses($time, &$action_message, $monster) {
		$buffs = [
			'attack-up' => 'has increased Attack',
			'defense-up' => 'has increased Defense',
			'speed-up' => 'has increased Speed',
			'evade-up' => 'has increased Evasion',
			'undying' => 'is Immortal'
		];
		foreach($buffs as $key=>$buff) {
			
			if(!empty($this->monsters[$monster['id']]['Monster']['buffs'][$key]['number_of_turns'])) {
				if($this->monsters[$monster['id']]['Monster']['buffs'][$key]['number_of_turns'] == 0) {
						unset($this->monsters[$monster['id']]['Monster']['buffs'][$key]);
						$this->addActionMessage($action_message, 'buff_lost',$this->monsters[$monster['id']]['Monster']['name'].' no longer '.$buff.'.');
				}else{
					$this->monsters[$monster['id']]['Monster']['buffs'][$key]['number_of_turns']--;
				}
			}elseif(!empty($this->monsters[$monster['id']]['Monster']['buffs'][$key])) {
				foreach($this->monsters[$monster['id']]['Monster']['buffs'][$key] as $index=>$details) {
					
					if($this->monsters[$monster['id']]['Monster']['buffs'][$key][$index]['number_of_turns'] == 0) {
						unset($this->monsters[$monster['id']]['Monster']['buffs'][$key][$index]);
						$this->addActionMessage($action_message, 'buff_lost',$this->monsters[$monster['id']]['Monster']['name'].' no longer '.$buff.'.');
					}else{
						$this->monsters[$monster['id']]['Monster']['buffs'][$key][$index]['number_of_turns']--;
					}
				}
				if(empty($this->monsters[$monster['id']]['Monster']['buffs'][$key]))
					unset($this->monsters[$monster['id']]['Monster']['buffs'][$key]);
			}
		}
		$debuffs = [
			'attack-down' => 'has decreased Attack',
			'defense-down' => 'has decreased Defense',
			'speed-down' => 'has decreased Speed',
			'evade-down' => 'has decreased Evasion'
		];
		foreach($debuffs as $key=>$debuff) {
			
			if(!empty($this->monsters[$monster['id']]['Monster']['debuffs'][$key])) {
				foreach($this->monsters[$monster['id']]['Monster']['debuffs'][$key] as $index=>$details) {
					if($details['number_of_turns'] == 0) {
						unset($this->monsters[$monster['id']]['Monster']['debuffs'][$key][$index]);
						$this->addActionMessage($action_message, 'debuff_lost',$this->monsters[$monster['id']]['Monster']['name'].' no longer '.$debuff.'.');
					}else{
						$this->monsters[$monster['id']]['Monster']['debuffs'][$key][$index]['number_of_turns']--;
					}
				}
				if(empty($this->monsters[$monster['id']]['Monster']['debuffs'][$key]))
					unset($this->monsters[$monster['id']]['Monster']['debuffs'][$key]);
			}
		}
	}
	
	private function processStatusHealingOverTime($time, &$action_message, $monster) {
		if($this->monsters[$monster['id']]['Monster']['buffs']['healing-over-time']['next_tick'] <= $time) {
			$healing = min($this->healingDampening($time, rand($this->monsters[$monster['id']]['Monster']['buffs']['healing-over-time']['amount_min'], $this->monsters[$monster['id']]['Monster']['buffs']['healing-over-time']['amount_max'])), $this->monsters[$monster['id']]['Monster']['max_health'] - $this->monsters[$monster['id']]['Monster']['current_health']);
			if(!empty($this->monsters[$monster['id']]['Monster']['debuffs']['infected'])) {
				$this->addActionMessage($action_message, 'healing_over_time', 'Healing Over Time for '.$this->monsters[$monster['id']]['Monster']['name'].' prevented by Infection.');
				$this->useInfect($time, $action_message, $monster);
			}elseif($healing > 0) {
				$this->monsters[$monster['id']]['Monster']['current_health'] += $healing;
				$this->addActionMessage($action_message, 'healing_over_time', $this->monsters[$monster['id']]['Monster']['name'].' heals for '.$healing.' from Healing Over Time.');
				$this->addHealthChangeLog($time, $monster, 'Healing', $healing);
			}
		}
		if(!empty($this->monsters[$monster['id']]['Monster']['buffs']['healing-over-time']) && $this->monsters[$monster['id']]['Monster']['buffs']['healing-over-time']['next_tick'] <= $time) {
			if($this->monsters[$monster['id']]['Monster']['buffs']['healing-over-time']['ends'] > $time) {
				$this->monsters[$monster['id']]['Monster']['buffs']['healing-over-time']['next_tick'] += 1000;
			}elseif($this->monsters[$monster['id']]['Monster']['buffs']['healing-over-time']['ends'] <= $time) {
				unset($this->monsters[$monster['id']]['Monster']['buffs']['healing-over-time']);
				$this->addActionMessage($action_message, 'buff_lost', $this->monsters[$monster['id']]['Monster']['name'].' is no longer Healing Over Time.');
			}
		}
	}
	
	private function processStatusPoisoned($time, &$action_message, $monster) {
		if($this->monsters[$monster['id']]['Monster']['debuffs']['poisoned']['next_tick'] <= $time) {
			$damage = rand($this->monsters[$monster['id']]['Monster']['debuffs']['poisoned']['amount_min'], $this->monsters[$monster['id']]['Monster']['debuffs']['poisoned']['amount_max']);
			$this->takeDamage($time, $action_message, $monster, $monster, 'Poison', $damage, false, $action_type = 'damage_over_time');
		}
		if(!empty($this->monsters[$monster['id']]['Monster']['debuffs']['poisoned']) && $this->monsters[$monster['id']]['Monster']['debuffs']['poisoned']['next_tick'] <= $time) {
			if($this->monsters[$monster['id']]['Monster']['debuffs']['poisoned']['ends'] > $time) {
				$this->monsters[$monster['id']]['Monster']['debuffs']['poisoned']['next_tick'] += 1000;
			}elseif($this->monsters[$monster['id']]['Monster']['debuffs']['poisoned']['ends'] <= $time) {
				unset($this->monsters[$monster['id']]['Monster']['debuffs']['poisoned']);
				$this->addActionMessage($action_message, 'debuff_lost', $this->monsters[$monster['id']]['Monster']['name'].' is no longer Poisoned.');
			}
		}
	}
	
	private function processStatusStunned($time, &$action_message, $monster) {
		if($this->monsters[$monster['id']]['Monster']['debuffs']['stunned']['ends'] <= $time) {
			unset($this->monsters[$monster['id']]['Monster']['debuffs']['stunned']);
			$this->addActionMessage($action_message, 'debuff_lost', $this->monsters[$monster['id']]['Monster']['name'].' is no longer Stunned.');
		}
	}
	
	private function processStatusAsleep($time, &$action_message, $monster) {
		if($this->monsters[$monster['id']]['Monster']['debuffs']['asleep']['ends'] <= $time) {
			unset($this->monsters[$monster['id']]['Monster']['debuffs']['asleep']);
			$this->addActionMessage($action_message, 'debuff_lost', $this->monsters[$monster['id']]['Monster']['name'].' woke up.');
			$this->monsters[$monster['id']]['Monster']['next_action_time'] = $time;
		}
	}
	private function processStatusFrozen($time, &$action_message, $monster, $shattered = false) {
		if($this->monsters[$monster['id']]['Monster']['debuffs']['frozen']['ends'] <= $time) {
			unset($this->monsters[$monster['id']]['Monster']['debuffs']['frozen']);
			if($shattered) {
				$this->addActionMessage($action_message, 'event', $this->monsters[$monster['id']]['Monster']['name'].' shattered free.');
			}else{
				$this->addActionMessage($action_message, 'debuff_lost', $this->monsters[$monster['id']]['Monster']['name'].' thawed.');
			}
			$this->monsters[$monster['id']]['Monster']['next_action_time'] = $time;
		}
	}
	private function processStatusConfused($time, &$action_message, $monster) {
		if($this->monsters[$monster['id']]['Monster']['debuffs']['confused']['ends'] <= $time) {
			unset($this->monsters[$monster['id']]['Monster']['debuffs']['confused']);
			$this->addActionMessage($action_message, 'debuff_lost', $this->monsters[$monster['id']]['Monster']['name'].' is no longer confused.');
		}
	}
	
	private function processStatusWetness($time, &$action_message, $monster) {
		
		if(!empty($this->monsters[$monster['id']]['Monster']['statuses']['drenched']['ends']) && $this->monsters[$monster['id']]['Monster']['statuses']['drenched']['ends'] <= $time) {
			$this->addActionMessage($action_message, 'debuff_lost', $this->monsters[$monster['id']]['Monster']['name'].' dried up a little.');
			unset($this->monsters[$monster['id']]['Monster']['statuses']['drenched']);
			$this->applyWet($time, $action_message, $monster, 'Soaked');
		}elseif(!empty($this->monsters[$monster['id']]['Monster']['statuses']['soaked']['ends']) && $this->monsters[$monster['id']]['Monster']['statuses']['soaked']['ends'] <= $time) {
			$this->addActionMessage($action_message, 'debuff_lost', $this->monsters[$monster['id']]['Monster']['name'].' dried up a little.');
			unset($this->monsters[$monster['id']]['Monster']['statuses']['soaked']);
			$this->applyWet($time, $action_message, $monster, 'Wet');
		}elseif(!empty($this->monsters[$monster['id']]['Monster']['statuses']['wet']['ends']) && $this->monsters[$monster['id']]['Monster']['statuses']['wet']['ends'] <= $time) {
			$this->addActionMessage($action_message, 'debuff_lost', $this->monsters[$monster['id']]['Monster']['name'].' dried up.');
			unset($this->monsters[$monster['id']]['Monster']['statuses']['wet']);
		}
	}
	
	private function processStatusBurned($time, &$action_message, $monster) {
		if($this->monsters[$monster['id']]['Monster']['debuffs']['burned']['ends'] <= $time) {
			unset($this->monsters[$monster['id']]['Monster']['debuffs']['burned']);
			$this->addActionMessage($action_message, 'debuff_lost', $this->monsters[$monster['id']]['Monster']['name'].' is no longer Burned.');
		}
	}
	private function processStatusPhoenixReborn($time, &$action_message, $monster) {
		if($this->monsters[$monster['id']]['Monster']['statuses']['phoenix-reborn']['next_tick'] <= $time) {
			$damage = ceil($this->monsters[$monster['id']]['Monster']['max_health'] / 20);
			$this->monsters[$monster['id']]['Monster']['statuses']['phoenix-reborn']['next_tick'] += 1000;
			$this->takeDamage($time, $action_message, $monster, $monster, 'Choas', $damage, false, 'damage_over_time');
		}
	}
	private function processStatusRotting($time, &$action_message, $monster) {
		if($this->monsters[$monster['id']]['Monster']['statuses']['rotting']['next_tick'] <= $time) {
			$damage = 2;
			$this->monsters[$monster['id']]['Monster']['statuses']['rotting']['next_tick'] += 1000;
			$this->takeDamage($time, $action_message, $monster, $monster, 'Undead', $damage, false, 'damage_over_time');
		}
	}
	
	private function takeDamage($time, &$action_message, $monster, $source, $type, $amount, $crit = false, $action_type = 'damage') {
		if($this->monsters[$monster['id']]['Monster']['current_health'] > 0) {
			if(!empty($this->monsters[$monster['id']]['Monster']['buffs']['bubble'])) {
				$this->addActionMessage($action_message, $action_type, $this->monsters[$monster['id']]['Monster']['name'].' absorbed the '.$type.' Damage with a Bubble.');
				unset($this->monsters[$monster['id']]['Monster']['buffs']['bubble']);
			}else{
				
				$this->addActionMessage($action_message, $action_type, $this->monsters[$monster['id']]['Monster']['name'].' takes '.$amount.' '.$type.' Damage'.($crit ? '(Critical Hit)' : '').'.');
				$this->monsters[$monster['id']]['Monster']['current_health'] -= $amount;
				$this->addHealthChangeLog($time, $monster, $type, -1 * $amount);
				
				//undying
				if($this->monsters[$monster['id']]['Monster']['current_health'] <= 0 && !empty($this->monsters[$monster['id']]['Monster']['buffs']['undying'])) {
					$this->addActionMessage($action_message, 'event', $this->monsters[$monster['id']]['Monster']['name'].' refuses to Die.');
					$this->monsters[$monster['id']]['Monster']['current_health'] = 1;
				}
				if(!empty($this->monsters[$monster['id']]['Monster']['statuses']['living-flesh']) && $amount >= round($this->monsters[$monster['id']]['Monster']['max_health'] * 0.15)) {
					$living_flesh = $this->living_flesh(round($amount / 2));
					$living_flesh['Monster']['team'] = $monster['Monster']['team'];
					$living_flesh['id'] = $this->getMonsterID();
					$living_flesh['summon'] = true;
					$this->monsters[$living_flesh['id']] = $living_flesh;
					$this->setupMonster($living_flesh, $time);
					$this->monsters[$living_flesh['id']]['Monster']['statuses']['rotting']['next_tick'] = $time + 1000; 
					$this->monsters[$living_flesh['id']]['Monster']['next_action_time'] = $time;
					$this->setupTeams();
					$this->addActionMessage($action_message, 'skill_result', 'A piece of Living Flesh falls off '.$this->monsters[$monster['id']]['Monster']['name']);
				}
				
				if($this->monsters[$monster['id']]['Monster']['current_health'] <= 0) {
					$this->handleDeath($time, $action_message, $monster);
				}else{
					if(!empty($this->monsters[$monster['id']]['Monster']['debuffs']['asleep'])) {
						$this->monsters[$monster['id']]['Monster']['debuffs']['asleep']['ends'] = $time;
						$this->processStatusAsleep($time, $action_message, $monster);
					}
					if(!empty($this->monsters[$monster['id']]['Monster']['debuffs']['frozen'])) {
						$this->monsters[$monster['id']]['Monster']['debuffs']['frozen']['ends'] = $time;
						$this->processStatusFrozen($time, $action_message, $monster, true);
					}
					if($type != 'Burn') {
						$this->triggerBurn($time, $action_message, $monster);
					}
				}	
			}
		}
	}
	
		private function handleDeath($time, &$action_message, $monster) {
		//check for phoenix
		if(!empty($this->monsters[$monster['id']]['Monster']['statuses']['phoenix'])) {
			$egg = $this->phoenixEgg();
			$egg['contains'] = $this->monsters[$monster['id']];
			$egg['id'] = $this->getMonsterID();
			$egg['Monster']['team'] = $monster['Monster']['team'];
			$this->monstersToRemove[] = $monster['id'];
			$this->monsters[$egg['id']] = $egg;
			$this->setupMonster($egg, $time);
			$this->setupTeams();
			$this->action_log[$time]['messages']['monster-'.$this->monsters[$egg['id']]['id']][][] = [
				'type' => 'event',
				'text' => $monster['Monster']['name'].' has turned into a strange egg.'
			];
		}else{
			$this->monsters[$monster['id']]['Monster']['current_health'] = 0;
			if(!empty($monster['summon'])) {
				$this->addActionMessage($action_message, 'event', $this->monsters[$monster['id']]['Monster']['name'].' was Destroyed.');
				$this->monstersToRemove[] = $monster['id'];
			}else{
				$this->addActionMessage($action_message, 'event', $this->monsters[$monster['id']]['Monster']['name'].' has Fainted.');
			}
			$this->monsters[$monster['id']]['Monster']['statuses'] = [];
			$this->monsters[$monster['id']]['Monster']['statuses']['fainted'] = [];
			$this->monsters[$monster['id']]['Monster']['buffs'] = [];
			$this->monsters[$monster['id']]['Monster']['debuffs'] = [];
			$this->action_log[$time]['casting']['monster-'.$this->monsters[$monster['id']]['id']] = [
				'status' => 'interrupted'
			];
		}
	}
	
	private function nextAction($time, $monster) {
		//check for enemies
		$hasEnemies = false;
		foreach($this->monsters[$monster['id']]['enemies'] as $enemy) {
			if($this->monsters[$enemy['id']]['Monster']['current_health'] > 0)
				$hasEnemies = true;
		}
		
		while($this->monsters[$monster['id']]['Monster']['next_action_time'] <= $time && $this->monsters[$monster['id']]['Monster']['current_health'] > 0 && $hasEnemies) {
			if(!empty($this->monsters[$monster['id']]['Monster']['next_use_skill'])) {
				$skill = $this->monsters[$monster['id']]['Monster']['next_use_skill'];
				if(!empty($skill['ultimate'])) {
					//ultimate finished casting
					$this->useUltimate($time, $monster, $skill);
				}else{
					//use the skill!
					$this->action_log[$time]['casting']['monster-'.$this->monsters[$monster['id']]['id']] = [
						'status' => 'completed',
						'name' => $skill['name']
					];
					$this->useSkill($time, $monster, $skill);
				}
			}else{
				//setup use of next skill
				if(!empty($this->monsters[$monster['id']]['skills'])) {
					$available_skill = false;
					foreach($this->monsters[$monster['id']]['skills'] as $skill) {
						if(empty($skill['disabled']))
							$available_skill = true;
					}
					$skill_index = $this->monsters[$monster['id']]['Monster']['next_action_skill'] % count($this->monsters[$monster['id']]['skills']);
					if(empty($this->monsters[$monster['id']]['skills'][$skill_index])) {
						$skill_index = 0;
					} 
					if(!empty($this->monsters[$monster['id']]['Monster']['debuffs']['confused'])) {
						$confused_skill_index = rand(0, count($this->monsters[$monster['id']]['skills']) - 1);
						if($confused_skill_index != $skill_index) {
							$skill_index = $confused_skill_index;
							$action_message = [];
							$this->addActionMessage($action_message, 'event', $this->monsters[$monster['id']]['Monster']['name'].' is confused and used a different skill than it should have.');
							$this->action_log[$time]['messages']['monster-'.$this->monsters[$monster['id']]['id']][] = $action_message;
						}
					} 
					if(!empty($this->monsters[$monster['id']]['skills'][$skill_index]) && $available_skill) {
						$skill = $this->monsters[$monster['id']]['skills'][$skill_index];
						if(!empty($skill['disabled'])) {
							
							$action_message = [];
							$this->addActionMessage($action_message, 'skill_result', $this->monsters[$monster['id']]['Monster']['name'].' can no longer use '.$skill['name'].'.');
							$this->action_log[$time]['messages']['monster-'.$this->monsters[$monster['id']]['id']][] = $action_message;
							$this->monsters[$monster['id']]['Monster']['next_action_skill']++;
							$this->monsters[$monster['id']]['Monster']['next_action_time'] += GLOBAL_DOWN_TIME * 2;
						}else{
							if(!empty($skill['ultimate'])) {
								//use ultimate
								$this->useUltimate($time, $monster, $skill);
							}else{
								//use normal skill!
								if($skill['cast_time'] == 0) {
									//use the skill!
									$this->action_log[$time]['casting']['monster-'.$this->monsters[$monster['id']]['id']] = [
										'status' => 'instant',
										'name' => $skill['name']
									];
									$this->useSkill($time, $monster, $skill);
								}else{
									$cast_time = $this->getCastTime($skill['cast_time'], $monster);
									$action_message = [];
									$this->addActionMessage($action_message, 'begin_cast', $this->monsters[$monster['id']]['Monster']['name'].' is casting '.$skill['name'].'.');
									$this->action_log[$time]['casting']['monster-'.$this->monsters[$monster['id']]['id']] = [
										'status' => 'start',
										'cast_time' => $cast_time,
										'name' => $skill['name']
									];
									$this->action_log[$time]['messages']['monster-'.$this->monsters[$monster['id']]['id']][] = $action_message;
									$this->monsters[$monster['id']]['Monster']['next_use_skill'] = $skill;
									$this->monsters[$monster['id']]['Monster']['next_action_time'] += $cast_time;
								}
							}
						}
					}else{
						$action_message = [];
						$this->addActionMessage($action_message, 'skill_result', $this->monsters[$monster['id']]['Monster']['name'].' has nothing to do.');
						$this->action_log[$time]['messages']['monster-'.$this->monsters[$monster['id']]['id']][] = $action_message;
						$this->monsters[$monster['id']]['Monster']['next_action_skill'] = 0;
						$this->monsters[$monster['id']]['Monster']['next_action_time'] += 5000;
						$this->tickStatuses($time, $action_message, $monster);
					}
				}else{
					$action_message = [];
					$this->addActionMessage($action_message, 'skill_result', $this->monsters[$monster['id']]['Monster']['name'].' has nothing to do.');
					$this->action_log[$time]['messages']['monster-'.$this->monsters[$monster['id']]['id']][] = $action_message;
					$this->monsters[$monster['id']]['Monster']['next_action_skill'] = 0;
					$this->monsters[$monster['id']]['Monster']['next_action_time'] += 5000;
					$this->tickStatuses($time, $action_message, $monster);
				}
			}
		}
	}
	
	private function useUltimate($time, $monster, $ultimate) {
		if($ultimate['starting_charges'] >= $ultimate['charges_needed']) {
			if($ultimate['cast_time'] == 0 || $this->monsters[$monster['id']]['Monster']['next_use_skill']['id'] == $ultimate['id']) {
				if($ultimate['cast_time'] != 0) {
					
					$this->action_log[$time]['casting']['monster-'.$this->monsters[$monster['id']]['id']] = [
						'status' => 'completed',
						'name' => $ultimate['name']
					];
				}else{
					$this->action_log[$time]['casting']['monster-'.$this->monsters[$monster['id']]['id']] = [
						'status' => 'instant',
						'name' => $ultimate['name']
					];
				}
				
				$ultimateData = $ultimate;
				
				//reset charge
				foreach($this->monsters[$monster['id']]['skills'] as $index => $skill) {
					if($skill == $ultimate) {
						$this->monsters[$monster['id']]['skills'][$index]['starting_charges'] = 0;
					}
				}
				$this->monsters[$monster['id']]['Monster']['next_action_skill']++;
				$this->monsters[$monster['id']]['Monster']['next_use_skill'] = null;
				$this->monsters[$monster['id']]['Monster']['next_action_time'] += round($ultimateData['down_time'] * 1000 + GLOBAL_DOWN_TIME);
				
				$action_message = [];
				$this->addActionMessage($action_message, 'skill_use', $this->monsters[$monster['id']]['Monster']['name'].' uses '.$ultimateData['name'].'.');
				foreach($ultimateData['SkillEffect'] as $skill_effect) {
					$targets = $this->getTargets($monster, $skill_effect['targets']);
					$this->processEffect($time, $action_message, $monster, $ultimateData['name'], $ultimateData['Type']['name'], $skill_effect, $targets);
					if(empty($skill_effect['missed'])) {
						foreach($skill_effect['SecondarySkillEffect'] as $secondary_skill_effect) {
							if($skill_effect['targets'] == $secondary_skill_effect['targets']) {
								$secondary_targets = $targets;
							}else{
								$secondary_targets = $this->getTargets($monster, $secondary_skill_effect['targets']);
							}
							$this->processEffect($time, $action_message, $monster, $ultimateData['name'], $ultimateData['Type']['name'], $secondary_skill_effect, $secondary_targets, true);
						}
					}
				}
				
				if($ultimateData['id'] == 3) {
					//count poison skills 
					$poison_count = 1;
					foreach($this->monsters[$monster['id']]['skills'] as $skill) {
						if($skill['Type']['name'] == 'Poison')
							$poison_count++;
					}
					foreach($this->monsters[$monster['id']]['enemies'] as $a_monster) {
						if(!empty($this->monsters[$a_monster['id']]['Monster']['debuffs']['poisoned'])) {
							$this->monsters[$a_monster['id']]['Monster']['debuffs']['poisoned']['amount_min'] *= round(1 + 0.05 * $poison_count);
							$this->monsters[$a_monster['id']]['Monster']['debuffs']['poisoned']['amount_max'] *= round(1 + 0.05 * $poison_count);
							$this->monsters[$a_monster['id']]['Monster']['debuffs']['poisoned']['ends'] += $poison_count * 1000;
							$this->addActionMessage($action_message, 'debuff_changed', $this->monsters[$a_monster['id']]['Monster']['name'].' had its poison extended by '.$poison_count.' second'.($poison_count == 1 ? '' : 's').'.');
						}
					}
				}
				
				if($ultimateData['id'] == 4) {
					
					$amount = ($this->monsters[$monster['id']]['Monster']['max_health'] - $this->monsters[$monster['id']]['Monster']['current_health']) / 2;
					$skill_effect = [
						'effect' => 'Magical Damage',
						'amount_min' => $amount,
						'amount_max' => $amount,
						'targets' => 'Single Enemy',
						'chance' => 100
					];
					$targets = $this->getTargets($monster, $skill_effect['targets']);
					$this->processEffect($time, $action_message, $monster, $ultimateData['name'], $ultimateData['Type']['name'], $skill_effect, $targets);
				}
				
				
				
				if($ultimateData['id'] == 5) {
					if($this->monsters[$monster['id']]['Monster']['current_health'] > 1) {
						$this->monsters[$monster['id']]['Monster']['max_health'] = floor($this->monsters[$monster['id']]['Monster']['max_health'] / 2);
						$this->monsters[$monster['id']]['Monster']['current_health'] = floor($this->monsters[$monster['id']]['Monster']['current_health'] / 2);
						if(empty($this->monsters[$monster['id']]['Monster']['statuses']['split'])) {
							$this->monsters[$monster['id']]['Monster']['statuses']['split']['stacks'] = 1;
						}else{
							$this->monsters[$monster['id']]['Monster']['statuses']['split']['stacks']++;
						}
						$this->addActionMessage($action_message, 'event', $this->monsters[$monster['id']]['Monster']['name'].' splits into two.');
						$clone = $this->monsters[$monster['id']];
						$clone['id'] = $this->getMonsterID();
						$clone['Monster']['name'] = 'Copy of '.$this->monsters[$monster['id']]['Monster']['name'];
						$clone['summon'] = true;
						$this->monsters[$clone['id']] = $clone;
						$this->setupTeams();
					}else{
						$this->addActionMessage($action_message, 'skill_result', $ultimateData['name'].' had no effect.');
					}
				}
				
				if($ultimateData['id'] == 6) {
					foreach($this->monsters as $a_monster) {
						if($a_monster['Type']['name'] != 'Water') {
							$this->applyWet($time, $action_message, $a_monster);
						}
					}
					if(empty($this->environment['status']['whirlpool'])) {
						$this->addActionMessage($action_message, 'event', 'A Whirlpool appears.');
						$this->addActionMessage($action_message, 'environment_change', 'Water type skills do 20% more damage.');
						$this->environment['status']['whirlpool'] = [
							'stacks' => 1,
							'ends' => 0
						];
					}else{
						$this->environment['status']['whirlpool']['stacks']++;
						$this->addActionMessage($action_message, 'event', 'The Whirlpool grows in size.');
						$this->addActionMessage($action_message, 'environment_change', 'Water type skills do '.($this->environment['status']['whirlpool']['stacks'] * 20).'% more damage.');
					}
				}
				
				if($ultimateData['id'] == 7) {
					$elements = [
						'Fire',
						'Water',
						'Earth',
						'Electric'
					];
					foreach($elements as $element) {
						$hasSkill = false;
						foreach($this->monsters[$monster['id']]['skills'] as $skill) {
							if(empty($skill['ultimate']) && $skill['Type']['name'] == $element) {
								$hasSkill = true;
							}
						}
						if($hasSkill) {
							$amount = 7;
							$skill_effect = [
								'effect' => 'Magical Damage',
								'amount_min' => $amount,
								'amount_max' => $amount,
								'targets' => 'Single Enemy',
								'chance' => 10000
							];
							$targets = $this->getTargets($monster, $skill_effect['targets']);
							$this->processEffect($time, $action_message, $monster, $ultimateData['name'], $element, $skill_effect, $targets);
						}
					}
				}
				
				if($ultimateData['id'] == 9) {
					$effects = rand(0,3);
					for($i=0; $i < $effects; $i++) {
						$wild_effects = $this->wildEffects();
						$wild_effect = array_rand($wild_effects);
						
						$skill_effect = [
							'effect' => $wild_effect,
							'amount_min' => $wild_effects[$wild_effect]['amount_min'],
							'amount_max' => $wild_effects[$wild_effect]['amount_max'],
							'duration' => $wild_effects[$wild_effect]['duration'],
							'targets' => $wild_effects[$wild_effect]['targets'][rand(0,count($wild_effects[$wild_effect]['targets']) - 1)],
							'chance' => 10000
						];
						$targets = $this->getTargets($monster, $skill_effect['targets']);
						
						$this->processEffect($time, $action_message, $monster, $ultimateData['name'], 'Wild', $skill_effect, $targets);
					}
					if($effects == 0) {
						$this->addActionMessage($action_message, 'skill_result', $ultimateData['name'].' fizzles.');
					}
				}
				/*
				if($ultimateData['id'] == 10) {
					$targets_name = 'Single Enemy';
					$targets = $this->getTargets($monster, $targets_name);
					foreach($targets as $target) {
						if(!empty($this->monsters[$target['id']]['skills'])) {
							$lose_skill_index = rand(0,count($this->monsters[$target['id']]['skills'])-1);
							$this->addActionMessage($action_message, 'skill_result', $this->monsters[$target['id']]['Monster']['name'].' can no longer use '.$this->monsters[$target['id']]['skills'][$lose_skill_index]['name']);
							unset($this->monsters[$target['id']]['skills'][$lose_skill_index]);
							$this->monsters[$target['id']]['skills'] = array_values($this->monsters[$target['id']]['skills']);
						}
					}
				}
				*/
				if($ultimateData['id'] == 10) {
					$targets_name = 'Single Enemy';
					$targets = $this->getTargets($monster, $targets_name);
					foreach($targets as $target) {
						if(!empty($this->monsters[$target['id']]['skills'])) {
							$available_skills = [];
							foreach($this->monsters[$target['id']]['skills'] as $index => $target_skill) {
								if(empty($target_skill['ultimate']) && empty($target_skill['disabled'])) {
									$available_skills[] = $index;
								}
							}
							if(!empty($available_skills)) {
								$random_index = array_rand($available_skills);
								$lose_skill_index = $available_skills[$random_index];
								$this->addActionMessage($action_message, 'skill_result', $this->monsters[$target['id']]['Monster']['name'].' can no longer use '.$this->monsters[$target['id']]['skills'][$lose_skill_index]['name']);
								$this->monsters[$target['id']]['skills'][$lose_skill_index]['disabled'] = 1;
							}else{
								$this->addActionMessage($action_message, 'skill_result', $this->monsters[$target['id']]['Monster']['name'].' does not have any more skills to disable.');
							}
						}
					}
				}
				
				if($ultimateData['id'] == 12) {
					$totem = $this->randomTotem($action_message);
					$totem['Monster']['team'] = $monster['Monster']['team'];
					$totem['id'] = $this->getMonsterID();
					$totem['summon'] = true;
					$this->monsters[$totem['id']] = $totem;
					$this->setupMonster($totem, $time);
					$this->monsters[$totem['id']]['Monster']['next_action_time'] = $time;
					$this->setupTeams();
				}
				
				if($ultimateData['id'] == 'Phoenix Rebirth') {
					$reborn = $monster['contains'];
					$reborn['id'] = $this->getMonsterID();
					$reborn['Monster']['current_health'] = round($reborn['Monster']['max_health'] / 2);
					$this->monstersToRemove[] = $monster['id'];
					unset($reborn['skills']);
					$this->monsters[$reborn['id']] = $reborn;
					$this->setupMonster($reborn, $time);
					$this->setupTeams();
					unset($this->monsters[$reborn['id']]['Monster']['statuses']['phoenix']);
					$this->monsters[$reborn['id']]['Monster']['statuses']['phoenix-reborn']['next_tick'] = $time + 1000; 
					$this->action_log[$time]['messages']['monster-'.$this->monsters[$reborn['id']]['id']][][] = [
						'type' => 'event',
						'text' => $reborn['Monster']['name'].' has been reborn and is stronger than ever, but is slowly burning away.'
					];
				}
				
				if($ultimateData['id'] == 15) {
					if(!empty($this->monsters[$monster['id']]['Monster']['statuses']['red-belt'])) {
						unset($this->monsters[$monster['id']]['Monster']['statuses']['red-belt']);
						$this->monsters[$monster['id']]['Monster']['statuses']['black-belt'] = true;
						$this->karateTraining($monster, true);
						foreach($this->monsters[$monster['id']]['skills'] as $index => $a_skill) {
							if($a_skill['id'] == 15 && !empty($a_skill['ultimate'])) {
								unset($this->monsters[$monster['id']]['skills'][$index]);
								break;
							}
						}
						$this->addActionMessage($action_message, 'skill_result', $monster['Monster']['name'].' is now a Black Belt.');
					}elseif(!empty($this->monsters[$monster['id']]['Monster']['statuses']['purple-belt'])) {
						unset($this->monsters[$monster['id']]['Monster']['statuses']['purple-belt']);
						$this->monsters[$monster['id']]['Monster']['statuses']['red-belt'] = true;
						$this->karateTraining($monster, false);
						$this->addActionMessage($action_message, 'skill_result', $monster['Monster']['name'].' is now a Red Belt.');
					}elseif(!empty($this->monsters[$monster['id']]['Monster']['statuses']['green-belt'])) {
						unset($this->monsters[$monster['id']]['Monster']['statuses']['green-belt']);
						$this->monsters[$monster['id']]['Monster']['statuses']['purple-belt'] = true;
						$this->karateTraining($monster, false);
						$this->addActionMessage($action_message, 'skill_result', $monster['Monster']['name'].' is now a Purple Belt.');
					}elseif(!empty($this->monsters[$monster['id']]['Monster']['statuses']['yellow-belt'])) {
						//skipping yellow
						unset($this->monsters[$monster['id']]['Monster']['statuses']['yellow-belt']);
						$this->monsters[$monster['id']]['Monster']['statuses']['green-belt'] = true;
						$this->karateTraining($monster, false);
						$this->addActionMessage($action_message, 'skill_result', $monster['Monster']['name'].' is now a Green Belt.');
					}elseif(!empty($this->monsters[$monster['id']]['Monster']['statuses']['white-belt'])) {
						unset($this->monsters[$monster['id']]['Monster']['statuses']['white-belt']);
						$this->monsters[$monster['id']]['Monster']['statuses']['green-belt'] = true;
						$this->karateTraining($monster, false);
						$this->addActionMessage($action_message, 'skill_result', $monster['Monster']['name'].' is now a Green Belt.');
					}
				}
				if($ultimateData['id'] == 17) {
					$doomsayer = $this->doomsayer();
					$doomsayer['Monster']['team'] = $monster['Monster']['team'];
					$doomsayer['id'] = $this->getMonsterID();
					$doomsayer['summon'] = true;
					$this->monsters[$doomsayer['id']] = $doomsayer;
					$this->setupMonster($doomsayer, $time);
					$this->monsters[$doomsayer['id']]['Monster']['next_action_time'] = $time;
					$this->setupTeams();
					$this->addActionMessage($action_message, 'skill_result', 'Recruits a Doomsayer.');
				}
				
				if($ultimateData['id'] == 'End of the World') {
					$this->addActionMessage($action_message, 'skill_result', $monster['Monster']['name'].' ends the World.');
					foreach($monster['enemies'] as $enemy) {
						$this->handleDeath($time, $action_message, $enemy);
					}
				}
				if($ultimateData['id'] == 18) {
					foreach($this->monsters[$monster['id']]['skills'] as $skill) {
						if(empty($skill['ultimate'])) {
							//use the skill!
							$this->addActionMessage($action_message, 'skill_use', $this->monsters[$monster['id']]['Monster']['name'].' uses '.$skill['name'].'.');
							foreach($skill['SkillEffect'] as $skill_effect) {
								$targets = $this->getTargets($monster, $skill_effect['targets']);
								$this->processEffect($time, $action_message, $monster, $skill['name'], $skill['Type']['name'], $skill_effect, $targets);
								if(empty($skill_effect['missed']) && $skill_effect['effect'] != 'Random Amount' && $skill_effect['effect'] != 'Consume') {
									foreach($skill_effect['SecondarySkillEffect'] as $secondary_skill_effect) {
										if($skill_effect['targets'] == $secondary_skill_effect['targets']) {
											$secondary_targets = $targets;
										}else{
											$secondary_targets = $this->getTargets($monster, $secondary_skill_effect['targets']);
										}
										$this->processEffect($time, $action_message, $monster, $skill['name'], $skill['Type']['name'], $secondary_skill_effect, $secondary_targets, true);
									}
								}
							}
						}
					}
				}
				
				
				//tick down any statuses
				$this->tickStatuses($time, $action_message, $monster);
				if(!empty($action_message))
					$this->action_log[$time]['messages']['monster-'.$this->monsters[$monster['id']]['id']][] = $action_message;
			}else{
				$cast_time = $this->getCastTime($ultimate['cast_time'], $monster);
				$action_message = [];
				$this->addActionMessage($action_message, 'begin_cast', $this->monsters[$monster['id']]['Monster']['name'].' is casting '.$ultimate['name'].'.');
				$this->action_log[$time]['casting']['monster-'.$this->monsters[$monster['id']]['id']] = [
					'status' => 'start',
					'cast_time' => $cast_time,
					'name' => $ultimate['name']
				];
				$this->action_log[$time]['messages']['monster-'.$this->monsters[$monster['id']]['id']][] = $action_message;
				$this->monsters[$monster['id']]['Monster']['next_use_skill'] = $ultimate;
				$this->monsters[$monster['id']]['Monster']['next_action_time'] += $cast_time;
			}
		}else{
			foreach($this->monsters[$monster['id']]['skills'] as $index => $skill) {
				if($skill == $ultimate) {
					$this->monsters[$monster['id']]['skills'][$index]['starting_charges']++;
					$action_message = [];
					$this->addActionMessage($action_message, 'ultimate_charging', $ultimate['name'].' is charging up. ('.$this->monsters[$monster['id']]['skills'][$index]['starting_charges'].'/'.($ultimate['charges_needed']).')');
					$this->tickStatuses($time, $action_message, $monster);
					if(!empty($action_message))
						$this->action_log[$time]['messages']['monster-'.$this->monsters[$monster['id']]['id']][] = $action_message;
					$this->monsters[$monster['id']]['Monster']['next_action_skill']++;
					$this->monsters[$monster['id']]['Monster']['next_use_skill'] = null;
					$this->monsters[$monster['id']]['Monster']['next_action_time'] += GLOBAL_DOWN_TIME;
				}
			}
		}
	}
	
	private function karateTraining($monster, $black_belt = false) {
		if(!$black_belt) {
			foreach($monster['skills'] as $skill_index => $skill) {
				if($skill['Type']['name'] == 'Fighting') {
					foreach($skill['SkillEffect'] as $skill_effect_index => $skill_effect) {
						if(in_array($skill_effect['targets'], ['Self'])) {
							$this->monsters[$monster['id']]['skills'][$skill_index]['SkillEffect'][$skill_effect_index]['duration'] += 1;
						}
						foreach($skill_effect['SecondarySkillEffect'] as $secondary_skill_effect_index => $secondary_skill_effect) {
							if(in_array($secondary_skill_effect['targets'], ['Self'])) {
								$this->monsters[$monster['id']]['skills'][$skill_index]['SkillEffect'][$skill_effect_index]['SecondarySkillEffect'][$secondary_skill_effect_index]['duration'] += 1;
							}
						}
					}
				}
			}
		}else{
			foreach($monster['skills'] as $skill_index => $skill) {
				if($skill['Type']['name'] == 'Fighting') {
					foreach($skill['SkillEffect'] as $skill_effect_index => $skill_effect) {
						if(in_array($skill_effect['targets'], ['Single Enemy','All Enemies'])) {
							$this->monsters[$monster['id']]['skills'][$skill_index]['SkillEffect'][$skill_effect_index]['amount_min'] *= 2;
							$this->monsters[$monster['id']]['skills'][$skill_index]['SkillEffect'][$skill_effect_index]['amount_max'] *= 2;
							$this->monsters[$monster['id']]['skills'][$skill_index]['SkillEffect'][$skill_effect_index]['chance'] = 10000;
						}
						foreach($skill_effect['SecondarySkillEffect'] as $secondary_skill_effect_index => $secondary_skill_effect) {
							if(in_array($secondary_skill_effect['targets'], ['Single Enemy','All Enemies'])) {
								$this->monsters[$monster['id']]['skills'][$skill_index]['SkillEffect'][$skill_effect_index]['SecondarySkillEffect'][$secondary_skill_effect_index]['amount_min'] *= 2;
								$this->monsters[$monster['id']]['skills'][$skill_index]['SkillEffect'][$skill_effect_index]['SecondarySkillEffect'][$secondary_skill_effect_index]['amount_max'] *= 2;
								$this->monsters[$monster['id']]['skills'][$skill_index]['SkillEffect'][$skill_effect_index]['SecondarySkillEffect'][$secondary_skill_effect_index]['chance'] = 10000;
							}
						}
					}
				}
			}
		}
	}
	
	private function getCastTime($cast_time, $monster) {
		//speed modifiers
		$speed_status_modifier = 1;
		if(!empty($caster['Monster']['buffs']['speed-up'])) {
			foreach($caster['Monster']['buffs']['speed-up'] as $speed_up_buff) {
				$speed_status_modifier *= (1 + $attack_up_buff['amount'] / 100);
			}
		}
		if(!empty($caster['Monster']['debuffs']['speed-down'])) {
			foreach($caster['Monster']['debuffs']['speed-down'] as $speed_down_buff) {
				$speed_status_modifier *= (1 - $speed_down_buff['amount'] / 100);
			}
		}
		return round($cast_time / $this->monsters[$monster['id']]['Stats']['speed'] * 1000 / $speed_status_modifier);
	}
	
	private function useSkill($time, $monster, $skill) {
		$action_message = [];
		$this->addActionMessage($action_message, 'skill_use', $this->monsters[$monster['id']]['Monster']['name'].' uses '.$skill['name'].'.');
		foreach($skill['SkillEffect'] as $skill_effect) {
			$targets = $this->getTargets($monster, $skill_effect['targets']);
			$this->processEffect($time, $action_message, $monster, $skill['name'], $skill['Type']['name'], $skill_effect, $targets);
			if(empty($skill_effect['missed']) && $skill_effect['effect'] != 'Random Amount' && $skill_effect['effect'] != 'Consume') {
				foreach($skill_effect['SecondarySkillEffect'] as $secondary_skill_effect) {
					if($skill_effect['targets'] == $secondary_skill_effect['targets']) {
						$secondary_targets = $targets;
					}else{
						$secondary_targets = $this->getTargets($monster, $secondary_skill_effect['targets']);
					}
					$this->processEffect($time, $action_message, $monster, $skill['name'], $skill['Type']['name'], $secondary_skill_effect, $secondary_targets, true);
				}
			}
		}
		//tick down any statuses
		$this->triggerBurn($time, $action_message, $monster);
		$this->tickStatuses($time, $action_message, $monster);
		$overload = false;
		if(!empty($skill['cast_again_chance'])) {
			//check for proc
			if(rand(1,10000) <= 100 * $skill['cast_again_chance']) {
				$this->addActionMessage($action_message, 'event', $skill['name'].' overloads and will be used again!');
				$overload = true;
				$this->monsters[$monster['id']]['Monster']['next_action_time'] = $time + 1;
			}
		}
		if(!empty($action_message))
			$this->action_log[$time]['messages']['monster-'.$this->monsters[$monster['id']]['id']][] = $action_message;
		
		$this->monsters[$monster['id']]['Monster']['next_use_skill'] = null;
		if($overload == false) {
			$this->monsters[$monster['id']]['Monster']['next_action_skill']++;
			$this->monsters[$monster['id']]['Monster']['next_action_time'] += round($skill['down_time'] * 1000 + GLOBAL_DOWN_TIME);
		}
		
	}
	
	private function processEffect($time, &$action_message, $monster, $ability_name, $type, &$skill_effect, $targets = [], $secondary = false) {
		$skill_effect['missed'] = false;
		if($skill_effect['effect'] == 'Random Amount') {
			foreach($targets as $target) {
				$amount = $this->calculateAmount($time, $skill_effect, $monster, $target);
				if($amount > 0) {
					//check for hit
					if($this->hit($type, $skill_effect, $monster, $target, $secondary)) {
						for($i=0; $i<$amount; $i++) {
							foreach($skill_effect['SecondarySkillEffect'] as $secondary_skill_effect) {
								if($skill_effect['targets'] == $secondary_skill_effect['targets']) {
									$secondary_targets = [$target];
								}else{
									$secondary_targets = $this->getTargets($monster, $secondary_skill_effect['targets']);
								}
								$this->processEffect($time, $action_message, $monster, $ability_name, $type, $secondary_skill_effect, $secondary_targets, true);
							}
						}
						$skill_effect['missed'] = true;
					}else{
						$this->addActionMessage($action_message, 'miss', $ability_name.' misses '.$this->monsters[$target['id']]['Monster']['name'].'.');
						$skill_effect['missed'] = true;
					}
				}else{
					$this->addActionMessage($action_message, 'skill_result', $ability_name.' had no effect on '.$this->monsters[$target['id']]['Monster']['name'].'.');
				}
			}
		}elseif($skill_effect['effect'] == 'Physical Damage') {
			foreach($targets as $target) {
				$damage = $this->calculateAmount($time, $skill_effect, $monster, $target);
				if($damage > 0) {
					//check for hit
					if($this->hit($type, $skill_effect, $monster, $target, $secondary)) {
						$isCrit = $this->checkForCrit($damage, $monster, $target);
						$this->takeDamage($time, $action_message, $target, $monster, 'Physical', $damage, $isCrit);
						$this->directHit($time, $action_message, $target);
					}elseif(!$secondary) {
						$this->addActionMessage($action_message, 'miss', $ability_name.' misses '.$this->monsters[$target['id']]['Monster']['name'].'.');
						$skill_effect['missed'] = true;
					}
				}else{
					$this->addActionMessage($action_message, 'skill_result', $ability_name.' had no effect on '.$this->monsters[$target['id']]['Monster']['name'].'.');
				}
			}
		}elseif($skill_effect['effect'] == 'Magical Damage') {
			foreach($targets as $target) {
				$damage = $this->calculateAmount($time, $skill_effect, $monster, $target);
				if($damage > 0) {
					//check for hit
					if($this->hit($type, $skill_effect, $monster, $target, $secondary)) {
						$isCrit = $this->checkForCrit($damage, $monster, $target);
						$this->takeDamage($time, $action_message, $target, $monster, $type, $damage, $isCrit);
						$this->directHit($time, $action_message, $target);
					}elseif(!$secondary) {
						$this->addActionMessage($action_message, 'miss', $ability_name.' misses '.$this->monsters[$target['id']]['Monster']['name'].'.');
						$skill_effect['missed'] = true;
					}
				}else{
					$this->addActionMessage($action_message, 'skill_result', $ability_name.' had no effect on '.$this->monsters[$target['id']]['Monster']['name'].'.');
				}
			}
		}elseif($skill_effect['effect'] == 'True Damage') {
			foreach($targets as $target) {
				$damage = $this->calculateAmount($time, $skill_effect, $monster, $target);
				if($damage > 0) {
					//check for hit
					if($this->hit($type, $skill_effect, $monster, $target, $secondary)) {
						$isCrit = $this->checkForCrit($damage, $monster, $target);
						$this->takeDamage($time, $action_message, $target, $monster, 'True', $damage, $isCrit);
						$this->directHit($time, $action_message, $target);
					}elseif(!$secondary) {
						$this->addActionMessage($action_message, 'miss', $ability_name.' misses '.$this->monsters[$target['id']]['Monster']['name'].'.');
						$skill_effect['missed'] = true;
					}
				}else{
					$this->addActionMessage($action_message, 'skill_result', $ability_name.' had no effect on '.$this->monsters[$target['id']]['Monster']['name'].'.');
				}
			}
		}elseif($skill_effect['effect'] == 'Leech') {
			foreach($targets as $target) {
				$damage = $this->calculateAmount($time, $skill_effect, $monster, $target);
				if($damage > 0) {
					//check for hit
					if($this->hit($type, $skill_effect, $monster, $target, $secondary)) {
						$isCrit = $this->checkForCrit($damage, $monster, $target);
						$this->takeDamage($time, $action_message, $target, $monster, 'Magical', $damage, $isCrit);
						$this->directHit($time, $action_message, $target);
						
						$healing = min($damage,($this->monsters[$monster['id']]['Monster']['max_health'] - $this->monsters[$monster['id']]['Monster']['current_health']));
						$this->monsters[$monster['id']]['Monster']['current_health'] += $healing;
						$this->addActionMessage($action_message, 'healing', $this->monsters[$monster['id']]['Monster']['name'].' heals for '.$healing.'.');
						$this->addHealthChangeLog($time, $monster, 'Healing', $healing);
					}elseif(!$secondary) {
						$this->addActionMessage($action_message, 'miss', $ability_name.' misses '.$this->monsters[$target['id']]['Monster']['name'].'.');
						$skill_effect['missed'] = true;
					}
				}else{
					$this->addActionMessage($action_message, 'skill_result', $ability_name.' had no effect on '.$this->monsters[$target['id']]['Monster']['name'].'.');
				}
			}
		}elseif($skill_effect['effect'] == 'Heal') {
			foreach($targets as $target) {
				$healing = $this->healingDampening($time, $this->calculateAmount($time, $skill_effect, $monster, $target));
				
				if(!empty($this->monsters[$target['id']]['Monster']['debuffs']['infected'])) {
					$this->addActionMessage($action_message, 'skill_result', 'Infection prevented healing from '.$ability_name);
					$this->useInfect($time, $action_message, $target);
				}elseif($healing > 0 && $this->monsters[$target['id']]['Monster']['current_health'] < $this->monsters[$target['id']]['Monster']['max_health']) {
					$isCrit = $this->checkForCrit($healing, $monster, $target);
					$healing = min($healing,($this->monsters[$target['id']]['Monster']['max_health'] - $this->monsters[$target['id']]['Monster']['current_health']));
					$this->monsters[$target['id']]['Monster']['current_health'] += $healing;
					$this->addActionMessage($action_message, 'healing', $this->monsters[$target['id']]['Monster']['name'].' heals for '.$healing.'.');
					$this->addHealthChangeLog($time, $target, 'Healing', $healing);
				}else{
					if(!$secondary) {
						$this->addActionMessage($action_message, 'healing', $ability_name.' had no effect on '.$this->monsters[$target['id']]['Monster']['name'].'.');
					}else{
						$this->addActionMessage($action_message, 'healing', 'The Heal from '.$ability_name.' had no effect on '.$this->monsters[$target['id']]['Monster']['name'].'.');
					}
				}
			}
		}elseif($skill_effect['effect'] == 'Heal Over Time') {
			foreach($targets as $target) {
				$this->applyHealOverTime($time, $action_message, $skill_effect, $target);
			}
		}elseif($skill_effect['effect'] == 'Poison') {
			foreach($targets as $target) {
				//check for hit
				if($this->hit($type, $skill_effect, $monster, $target, $secondary)) {
					$this->applyPoison($time, $action_message, $skill_effect, $target);
				}elseif(!$secondary) {
					$this->addActionMessage($action_message, 'miss', $ability_name.' misses '.$this->monsters[$target['id']]['Monster']['name'].'.');
					$skill_effect['missed'] = true;
				}
			}
		}elseif($skill_effect['effect'] == 'Infect') {
			foreach($targets as $target) {
				//check for hit
				if($this->hit($type, $skill_effect, $monster, $target, $secondary)) {
					$this->applyInfect($time, $action_message, $skill_effect, $target);
				}elseif(!$secondary) {
					$this->addActionMessage($action_message, 'miss', $ability_name.' misses '.$this->monsters[$target['id']]['Monster']['name'].'.');
					$skill_effect['missed'] = true;
				}
			}
		}elseif($skill_effect['effect'] == 'Bubble') {
			foreach($targets as $target) {
				//check for hit
				if(empty($this->monsters[$target['id']]['Monster']['buffs']['bubble'])) {
					$this->applyBubble($time, $action_message, $skill_effect, $target);
				}elseif(!$secondary) {
					$this->addActionMessage($action_message, 'skill_result', $ability_name.' had no effect on '.$this->monsters[$target['id']]['Monster']['name'].'.');
					$skill_effect['missed'] = true;
				}
			}
		}elseif($skill_effect['effect'] == 'Burn') {
			foreach($targets as $target) {
				//check for hit
				if($this->hit($type, $skill_effect, $monster, $target, $secondary)) {
					$this->applyBurn($time, $action_message, $skill_effect, $target);
				}elseif(!$secondary) {
					$this->addActionMessage($action_message, 'miss', $ability_name.' misses '.$this->monsters[$target['id']]['Monster']['name'].'.');
					$skill_effect['missed'] = true;
				}
			}
		}elseif($skill_effect['effect'] == 'Stun') {
			foreach($targets as $target) {
				//check for hit
				if($this->hit($type, $skill_effect, $monster, $target, $secondary)) {
					if($this->checkForCC($target)) {
						$this->applyStun($time, $action_message, $skill_effect, $target);
					}else{
						$this->addActionMessage($action_message, 'skill_result', $this->monsters[$target['id']]['Monster']['name'].' is immune to Control Effects.');
					}
				}elseif(!$secondary) {
					$this->addActionMessage($action_message, 'miss', $ability_name.' misses '.$this->monsters[$target['id']]['Monster']['name'].'.');
					$skill_effect['missed'] = true;
				}
			}
		}elseif($skill_effect['effect'] == 'Sleep') {
			foreach($targets as $target) {
				//check for hit
				if($this->hit($type, $skill_effect, $monster, $target, $secondary)) {
					if($this->checkForCC($target)) {
						$this->applySleep($time, $action_message, $skill_effect, $target);
					}else{
						$this->addActionMessage($action_message, 'skill_result', $this->monsters[$target['id']]['Monster']['name'].' is immune to Control Effects.');
					}
				}elseif(!$secondary) {
					$this->addActionMessage($action_message, 'miss', $ability_name.' misses '.$this->monsters[$target['id']]['Monster']['name'].'.');
					$skill_effect['missed'] = true;
				}
			}
		}elseif($skill_effect['effect'] == 'Freeze') {
			foreach($targets as $target) {
				//check for hit
				if($this->hit($type, $skill_effect, $monster, $target, $secondary)) {
					if($this->checkForCC($target)) {
						$this->applyFrozen($time, $action_message, $skill_effect, $target);
					}else{
						$this->addActionMessage($action_message, 'skill_result', $this->monsters[$target['id']]['Monster']['name'].' is immune to Control Effects.');
					}
				}elseif(!$secondary) {
					$this->addActionMessage($action_message, 'miss', $ability_name.' misses '.$this->monsters[$target['id']]['Monster']['name'].'.');
					$skill_effect['missed'] = true;
				}
			}
		}elseif($skill_effect['effect'] == 'Confuse') {
			foreach($targets as $target) {
				//check for hit
				if($this->hit($type, $skill_effect, $monster, $target, $secondary)) {
					$this->applyConfuse($time, $action_message, $skill_effect, $target);
				}elseif(!$secondary) {
					$this->addActionMessage($action_message, 'miss', $ability_name.' misses '.$this->monsters[$target['id']]['Monster']['name'].'.');
					$skill_effect['missed'] = true;
				}
			}
		}elseif($skill_effect['effect'] == 'Cleanse') {
			foreach($targets as $target) {
				if(!empty($this->monsters[$target['id']]['Monster']['debuffs'])) {
					$cleanse_count = rand($skill_effect['amount_min'],$skill_effect['amount_max']);
					$count = 0;
					while($cleanse_count > 0 && count($this->monsters[$target['id']]['Monster']['debuffs']) > 0) {
						$count++;
						$cleanse_status_key = array_rand($this->monsters[$target['id']]['Monster']['debuffs']);
						unset($this->monsters[$target['id']]['Monster']['debuffs'][$cleanse_status_key]);
					}
					$this->addActionMessage($action_message, 'skill_result', $count.' debuff'.($count == 1 ? ' was' : 's were').' cleansed.');
				}else{
					$this->addActionMessage($action_message, 'skill_result', $ability_name.' had no effect on '.$this->monsters[$target['id']]['Monster']['name'].'.');
					$skill_effect['missed'] = true;
				}
			}
		}elseif($skill_effect['effect'] == 'Purge') {
			foreach($targets as $target) {
				if(!empty($this->monsters[$target['id']]['Monster']['buffs'])) {
					$purge_count = rand($skill_effect['amount_min'],$skill_effect['amount_max']);
					$count = 0;
					while($purge_count > 0 && count($this->monsters[$target['id']]['Monster']['buffs']) > 0) {
						$count++;
						$purge_status_key = array_rand($this->monsters[$target['id']]['Monster']['buffs']);
						unset($this->monsters[$target['id']]['Monster']['buffs'][$purge_status_key]);
						$purge_count--;
					}
					$this->addActionMessage($action_message, 'skill_result', $count.' buff'.($count == 1 ? ' was' : 's were').' purged.');
				}else{
					$this->addActionMessage($action_message, 'skill_result', $ability_name.' had no effect on '.$this->monsters[$target['id']]['Monster']['name'].'.');
					$skill_effect['missed'] = true;
				}
			}
		}elseif($skill_effect['effect'] == 'Consume') {
			foreach($targets as $target) {
				if($this->hit($type, $skill_effect, $monster, $target, $secondary)) {
					$stack_count = 0;
					if($skill_effect['status'] == 'random_buff') {
						if(!empty($this->monsters[$target['id']]['Monster']['buffs'])) {
							$consuming = array_rand($this->monsters[$target['id']]['Monster']['buffs']);
							if(!empty($this->monsters[$target['id']]['Monster']['buffs'][$consuming]['stacks'])) {
								$stack_count += $this->monsters[$target['id']]['Monster']['buffs'][$consuming]['stacks'];
							}else{
								$stack_count++;
							}
							unset($this->monsters[$target['id']]['Monster']['buffs'][$consuming]);
						}
					}elseif($skill_effect['status'] == 'all_buffs') {
						if(!empty($this->monsters[$target['id']]['Monster']['buffs'])) {
							foreach($this->monsters[$target['id']]['Monster']['buffs'] as $consuming => $buff) {
								if(!empty($this->monsters[$target['id']]['Monster']['buffs'][$consuming]['stacks'])) {
									$stack_count += $this->monsters[$target['id']]['Monster']['buffs'][$consuming]['stacks'];
								}else{
									$stack_count++;
								}
								unset($this->monsters[$target['id']]['Monster']['buffs'][$consuming]);
							}
						}
					}elseif($skill_effect['status'] == 'random_debuff') {
						if(!empty($this->monsters[$target['id']]['Monster']['debuffs'])) {
							$consuming = array_rand($this->monsters[$target['id']]['Monster']['debuffs']);
							if(!empty($this->monsters[$target['id']]['Monster']['debuffs'][$consuming]['stacks'])) {
								$stack_count += $this->monsters[$target['id']]['Monster']['debuffs'][$consuming]['stacks'];
							}else{
								$stack_count++;
							}
							unset($this->monsters[$target['id']]['Monster']['debuffs'][$consuming]);
						}
					}elseif($skill_effect['status'] == 'all_debuffs') {
						if(!empty($this->monsters[$target['id']]['Monster']['debuffs'])) {
							foreach($this->monsters[$target['id']]['Monster']['debuffs'] as $consuming => $buff) {
								if(!empty($this->monsters[$target['id']]['Monster']['debuffs'][$consuming]['stacks'])) {
									$stack_count += $this->monsters[$target['id']]['Monster']['debuffs'][$consuming]['stacks'];
								}else{
									$stack_count++;
								}
								unset($this->monsters[$target['id']]['Monster']['debuffs'][$consuming]);
							}
						}
					}elseif($skill_effect['status'] == 'random_buff_debuff') {
						if(!empty($this->monsters[$target['id']]['Monster']['debuffs']) || !empty($this->monsters[$target['id']]['Monster']['buffs'])) {
							$all_statuses = $this->monsters[$target['id']]['Monster']['buffs'] + $this->monsters[$target['id']]['Monster']['debuffs'];
							$consuming = array_rand($all_statuses);
							if(!empty($this->monsters[$target['id']]['Monster']['buffs'][$consuming])) {
								if(!empty($this->monsters[$target['id']]['Monster']['buffs'][$consuming]['stacks'])) {
									$stack_count += $this->monsters[$target['id']]['Monster']['buffs'][$consuming]['stacks'];
								}else{
									$stack_count++;
								}
								unset($this->monsters[$target['id']]['Monster']['buffs'][$consuming]);
							}
							if(!empty($this->monsters[$target['id']]['Monster']['debuffs'][$consuming])) {
								if(!empty($this->monsters[$target['id']]['Monster']['debuffs'][$consuming]['stacks'])) {
									$stack_count += $this->monsters[$target['id']]['Monster']['debuffs'][$consuming]['stacks'];
								}else{
									$stack_count++;
								}
								unset($this->monsters[$target['id']]['Monster']['debuffs'][$consuming]);
							}
						}
					}elseif($skill_effect['status'] == 'all_buffs_debuffs') {
						if(!empty($this->monsters[$target['id']]['Monster']['buffs'])) {
							foreach($this->monsters[$target['id']]['Monster']['buffs'] as $consuming => $buff) {
								if(!empty($this->monsters[$target['id']]['Monster']['buffs'][$consuming]['stacks'])) {
									$stack_count += $this->monsters[$target['id']]['Monster']['buffs'][$consuming]['stacks'];
								}else{
									$stack_count++;
								}
								unset($this->monsters[$target['id']]['Monster']['buffs'][$consuming]);
							}
						}
						if(!empty($this->monsters[$target['id']]['Monster']['debuffs'])) {
							foreach($this->monsters[$target['id']]['Monster']['debuffs'] as $consuming => $buff) {
								if(!empty($this->monsters[$target['id']]['Monster']['debuffs'][$consuming]['stacks'])) {
									$stack_count += $this->monsters[$target['id']]['Monster']['debuffs'][$consuming]['stacks'];
								}else{
									$stack_count++;
								}
								unset($this->monsters[$target['id']]['Monster']['debuffs'][$consuming]);
							}
						}
					}else{
						if(!empty($this->monsters[$target['id']]['Monster']['buffs'][$skill_effect['status']])) {
							$consuming = $skill_effect['status'];
							if(!empty($this->monsters[$target['id']]['Monster']['buffs'][$consuming]['stacks'])) {
								$stack_count += $this->monsters[$target['id']]['Monster']['buffs'][$consuming]['stacks'];
							}else{
								$stack_count++;
							}
							unset($this->monsters[$target['id']]['Monster']['buffs'][$consuming]);
						}
						if(!empty($this->monsters[$target['id']]['Monster']['debuffs'][$skill_effect['status']])) {
							$consuming = $skill_effect['status'];
							if(!empty($this->monsters[$target['id']]['Monster']['debuffs'][$consuming]['stacks'])) {
								$stack_count += $this->monsters[$target['id']]['Monster']['debuffs'][$consuming]['stacks'];
							}else{
								$stack_count++;
							}
							unset($this->monsters[$target['id']]['Monster']['debuffs'][$consuming]);
						}
					}
					if($stack_count > 0) {
						foreach($skill_effect['SecondarySkillEffect'] as $secondary_skill_effect) {
							if($skill_effect['targets'] == $secondary_skill_effect['targets']) {
								$secondary_targets = [$target];
							}else{
								$secondary_targets = $this->getTargets($monster, $secondary_skill_effect['targets']);
							}
							if($secondary_skill_effect['duration'] > 0) {
								$secondary_skill_effect['duration'] *= $stack_count;
								$this->processEffect($time, $action_message, $monster, $ability_name, $type, $secondary_skill_effect, $secondary_targets, true);
							}else{
								$secondary_skill_effect['amount_min'] *= $stack_count;
								$secondary_skill_effect['amount_max'] *= $stack_count;
								$this->processEffect($time, $action_message, $monster, $ability_name, $type, $secondary_skill_effect, $secondary_targets, true);
							}
						}
					}else{
						$this->addActionMessage($action_message, 'skill_result', $ability_name.' had no effect on '.$this->monsters[$target['id']]['Monster']['name'].'.');
						$skill_effect['missed'] = true;
					}
				}else{
					$this->addActionMessage($action_message, 'miss', $ability_name.' misses '.$this->monsters[$target['id']]['Monster']['name'].'.');
					$skill_effect['missed'] = true;
				}
			}
		}elseif($skill_effect['effect'] == 'Attack Up') {
			foreach($targets as $target) {
				$amount = $this->calculateAmount($time, $skill_effect, $monster, $target);
				$this->addActionMessage($action_message, 'buff_gained', $this->monsters[$target['id']]['Monster']['name'].' Attack Increased by '.$amount.'%.');
				$this->monsters[$target['id']]['Monster']['buffs']['attack-up'][] = [
					'amount' => $amount,
					'number_of_turns' => $skill_effect['duration']
				];
			}
		}elseif($skill_effect['effect'] == 'Attack Down') {
			foreach($targets as $target) {
				//check for hit
				if($this->hit($type, $skill_effect, $monster, $target, $secondary)) {
					$amount = $this->calculateAmount($time, $skill_effect, $monster, $target);
					$this->addActionMessage($action_message, 'debuff_gained', $this->monsters[$target['id']]['Monster']['name'].' Attack Decreased by '.$amount.'%.');
					$this->monsters[$target['id']]['Monster']['debuffs']['attack-down'][] = [
						'amount' => $amount,
						'number_of_turns' => $skill_effect['duration']
					];
				}elseif(!$secondary) {
					$this->addActionMessage($action_message, 'miss', $ability_name.' misses '.$this->monsters[$target['id']]['Monster']['name'].'.');
					$skill_effect['missed'] = true;
				}
			}
		}elseif($skill_effect['effect'] == 'Defense Up') {
			foreach($targets as $target) {
				$amount = $this->calculateAmount($time, $skill_effect, $monster, $target);
				$this->addActionMessage($action_message, 'buff_gained', $this->monsters[$target['id']]['Monster']['name'].' Defense Increased by '.$amount.'%.');
				$this->monsters[$target['id']]['Monster']['buffs']['defense-up'][] = [
					'amount' => $amount,
					'number_of_turns' => $skill_effect['duration']
				];
			}
		}elseif($skill_effect['effect'] == 'Defense Down') {
			foreach($targets as $target) {
				//check for hit
				if($this->hit($type, $skill_effect, $monster, $target, $secondary)) {
					$amount = $this->calculateAmount($time, $skill_effect, $monster, $target);
					$this->addActionMessage($action_message, 'debuff_gained', $this->monsters[$target['id']]['Monster']['name'].' Defense Decreased by '.$amount.'%.');
					$this->monsters[$target['id']]['Monster']['debuffs']['defense-down'][] = [
						'amount' => $amount,
						'number_of_turns' => $skill_effect['duration']
					];
				}elseif(!$secondary) {
					$this->addActionMessage($action_message, 'miss', $ability_name.' misses '.$this->monsters[$target['id']]['Monster']['name'].'.');
					$skill_effect['missed'] = true;
				}
			}
		}elseif($skill_effect['effect'] == 'Speed Up') {
			foreach($targets as $target) {
				$amount = $this->calculateAmount($time, $skill_effect, $monster, $target);
				$this->addActionMessage($action_message, 'buff_gained', $this->monsters[$target['id']]['Monster']['name'].' Speed Increased by '.$amount.'%.');
				$this->monsters[$target['id']]['Monster']['buffs']['speed-up'][] = [
					'amount' => $amount,
					'number_of_turns' => $skill_effect['duration']
				];
			}
		}elseif($skill_effect['effect'] == 'Speed Down') {
			foreach($targets as $target) {
				//check for hit
				if($this->hit($type, $skill_effect, $monster, $target, $secondary)) {
					$amount = $this->calculateAmount($time, $skill_effect, $monster, $target);
					$this->addActionMessage($action_message, 'debuff_gained', $this->monsters[$target['id']]['Monster']['name'].' Speed Decreased by '.$amount.'%.');
					$this->monsters[$target['id']]['Monster']['debuffs']['speed-down'][] = [
						'amount' => $amount,
						'number_of_turns' => $skill_effect['duration']
					];
				}elseif(!$secondary) {
					$this->addActionMessage($action_message, 'miss', $ability_name.' misses '.$this->monsters[$target['id']]['Monster']['name'].'.');
					$skill_effect['missed'] = true;
				}
			}
		}elseif($skill_effect['effect'] == 'Evade Up') {
			foreach($targets as $target) {
				$amount = $this->calculateAmount($time, $skill_effect, $monster, $target);
				$this->addActionMessage($action_message, 'buff_gained', $this->monsters[$target['id']]['Monster']['name'].' Evasion Increased by '.$amount.'%.');
				$this->monsters[$target['id']]['Monster']['buffs']['evade-up'][] = [
					'amount' => $amount,
					'number_of_turns' => $skill_effect['duration']
				];
			}
		}elseif($skill_effect['effect'] == 'Evade Down') {
			foreach($targets as $target) {
				//check for hit
				if($this->hit($type, $skill_effect, $monster, $target, $secondary)) {
					$amount = $this->calculateAmount($time, $skill_effect, $monster, $target);
					$this->addActionMessage($action_message, 'debuff_gained', $this->monsters[$target['id']]['Monster']['name'].' Evasion Decreased by '.$amount.'%.');
					$this->monsters[$target['id']]['Monster']['debuffs']['evade-down'][] = [
						'amount' => $amount,
						'number_of_turns' => $skill_effect['duration']
					];
				}elseif(!$secondary) {
					$this->addActionMessage($action_message, 'miss', $ability_name.' misses '.$this->monsters[$target['id']]['Monster']['name'].'.');
					$skill_effect['missed'] = true;
				}
			}
		}elseif($skill_effect['effect'] == 'Wet') {
			foreach($targets as $target) {
				//check for hit
				if($this->hit($type, $skill_effect, $monster, $target, $secondary)) {
					$this->applyWet($time, $action_message, $target);
				}elseif(!$secondary) {
					$this->addActionMessage($action_message, 'miss', $ability_name.' misses '.$this->monsters[$target['id']]['Monster']['name'].'.');
					$skill_effect['missed'] = true;
				}
			}
		}elseif($skill_effect['effect'] == 'Delay') {
			foreach($targets as $target) {
				//check for hit
				if($this->hit($type, $skill_effect, $monster, $target, $secondary)) {
					$this->applyDelay($time, $action_message, $skill_effect, $target);
				}elseif(!$secondary) {
					$this->addActionMessage($action_message, 'miss', $ability_name.' misses '.$this->monsters[$target['id']]['Monster']['name'].'.');
					$skill_effect['missed'] = true;
				}
			}
		}elseif($skill_effect['effect'] == 'Undying') {
			foreach($targets as $target) {
				$this->applyUndying($time, $action_message, $skill_effect, $target);
			}
		}
	}
	
	private function getTargets($monster, $targets, $required_state = ['alive']) {
		$valid_enemies = [];
		foreach($this->monsters[$monster['id']]['enemies'] as $enemy) {
			if(($this->monsters[$enemy['id']]['Monster']['current_health'] > 0 && in_array('alive', $required_state)) || ($this->monsters[$enemy['id']]['Monster']['current_health'] <= 0 && in_array('fainted', $required_state)))
				$valid_enemies[] = $enemy;
		}
		$valid_allies = [];
		foreach($this->monsters[$monster['id']]['allies'] as $ally) {
			if(($this->monsters[$ally['id']]['Monster']['current_health'] > 0 && in_array('alive', $required_state)) || ($this->monsters[$ally['id']]['Monster']['current_health'] <= 0 && in_array('fainted', $required_state)))
				$valid_allies[] = $ally;
		}
		$targetsArray = [];
		if($targets == 'Single Enemy' && !empty($valid_enemies)) {
			$targetsArray[] = $valid_enemies[rand(0,count($valid_enemies) - 1)];
		}elseif($targets == 'Self') {
			$targetsArray[] = $monster;
		}elseif($targets == 'All Enemies') {
			foreach($valid_enemies as $enemy) {
				$targetsArray[] = $enemy;
			}
		}elseif($targets == 'Everyone') {
			$targetsArray[] = $monster;
			foreach($valid_enemies as $enemy) {
				$targetsArray[] = $enemy;
			}
			foreach($valid_allies as $ally) {
				$targetsArray[] = $ally;
			}
		}elseif($targets == 'All Allies') {
			$targetsArray[] = $monster;
			foreach($valid_allies as $ally) {
				$targetsArray[] = $ally;
			}
		}
		return $targetsArray;
	}
	
	private function hit($type, $skill_effect, $attacker, $defender, $secondary) {
		if($attacker['id'] == $defender['id'])
			return true;
		if($skill_effect['chance'] == 100 && $secondary)
			return true;
		$chanceToHit = round(($skill_effect['chance'] + ($this->monsters[$attacker['id']]['Stats']['hitModifier'] - $this->monsters[$defender['id']]['Stats']['evadeModifier']) * 100) * 1000);
		if(!empty($defender['Monster']['statuses']['drenched'])) {
			if($type == 'Electric') {
				$chanceToHit += 60000;
			}
			if($skill_effect['effect'] == 'Freeze') {
				$chanceToHit += 60000;
			}
		}elseif(!empty($defender['Monster']['statuses']['soaked'])) {
			if($type == 'Electric') {
				$chanceToHit += 40000;
			}
			if($skill_effect['effect'] == 'Freeze') {
				$chanceToHit += 60000;
			}
		}elseif(!empty($defender['Monster']['statuses']['wet'])) {
			if($type == 'Electric') {
				$chanceToHit += 20000;
			}
			if($skill_effect['effect'] == 'Freeze') {
				$chanceToHit += 60000;
			}
		}
		$evade_status_modifier = 0;
		if(!empty($caster['Monster']['buffs']['evade-up'])) {
			foreach($caster['Monster']['buffs']['evade-up'] as $evade_up_buff) {
				$evade_status_modifier += $evade_up_buff['amount'] * 1000;
			}
		}
	
		if(!empty($caster['Monster']['debuffs']['evade-down'])) {
			foreach($caster['Monster']['debuffs']['evade-down'] as $evade_down_buff) {
				$evade_status_modifier -= $evade_down_buff['amount'] * 1000;
			}
		}
		
		if(rand(1,100000) + $evade_status_modifier <= $chanceToHit) {
			return true;
		}else{
			return false;
		}
	}
	
	private function checkForCrit(&$amount, $caster, $target) {
		if(!empty($this->monsters[$target['id']]['Monster']['debuffs']['frozen']))
			return true;
		$chanceToCrit = $this->monsters[$caster['id']]['Stats']['criticalChance'] * 100 * 1000;
		if(rand(1,100000) <= $chanceToCrit) {
			$amount = round(1.5 * $amount);
			return true;
		}else{
			return false;
		}
	}
	private function checkForCC($monster) {
		if($this->monsters[$monster['id']]['cc_count'] == -1) {
			//monster is permanently immune to CC
			return false;
		}
		if($this->monsters[$monster['id']]['cc_count'] > CC_IN_A_ROW_LIMIT) {
			$this->monsters[$monster['id']]['cc_count'] = 0;
			return false;
		}else{
			$this->monsters[$monster['id']]['cc_count']++;
			return true;
		}
	}
	
	private function calculateAmount($time, $skill_effect, $caster, $target) {
		$amount = 0;
		//get higher rolls from luck
		$highest_amount_found = false;
		while($highest_amount_found == false) {
			$amount = max($amount,rand($skill_effect['amount_min'],$skill_effect['amount_max']));
			if(rand(1,100) > (100 * $this->monsters[$caster['id']]['Stats']['rerollChance']) || $skill_effect['amount_min'] == $skill_effect['amount_max'])
				$highest_amount_found = true;
		}
		if($skill_effect['effect'] == 'Physical Damage' || $skill_effect['effect'] == 'Magical Damage' || $skill_effect['effect'] == 'Leech') {
			
			//calcuate attack modifier
			$attack_status_modifier = 1;
			if(!empty($this->monsters[$caster['id']]['Monster']['buffs']['attack-up'])) {
				foreach($this->monsters[$caster['id']]['Monster']['buffs']['attack-up'] as $attack_up_buff) {
					$attack_status_modifier *= (1 + $attack_up_buff['amount'] / 100);
				}
			}
			if(!empty($this->monsters[$caster['id']]['Monster']['debuffs']['attack-down'])) {
				foreach($this->monsters[$caster['id']]['Monster']['debuffs']['attack-down'] as $attack_down_buff) {
					$attack_status_modifier *= (1 - $attack_down_buff['amount'] / 100);
				}
			}
			$defender_status_modifier = 1;
			if(!empty($this->monsters[$target['id']]['Monster']['buffs']['defense-up'])) {
				foreach($this->monsters[$target['id']]['Monster']['buffs']['defense-up'] as $defense_up_buff) {
					$defender_status_modifier *= (1 + $defense_up_buff['amount'] / 100);
				}
			}
			if(!empty($this->monsters[$target['id']]['Monster']['debuffs']['defense-down'])) {
				foreach($this->monsters[$target['id']]['Monster']['debuffs']['defense-down'] as $defense_down_buff) {
					$defender_status_modifier *= (1 - $defense_down_buff['amount'] / 100);
				}
			}
			//dont divide by zero!
			if($this->monsters[$target['id']]['Stats']['physicalDefenseModifier'] == 0 || $defender_status_modifier == 0)
				$amount = 1000000000;
			
			if($skill_effect['effect'] == 'Physical Damage') {
				$amount = round($amount * $this->monsters[$caster['id']]['Stats']['physicalAttackModifier'] * $attack_status_modifier / $this->monsters[$target['id']]['Stats']['physicalDefenseModifier'] / $defender_status_modifier);
			}elseif($skill_effect['effect'] == 'Magical Damage' || $skill_effect['effect'] == 'Leech') {
				$amount = round($amount * $this->monsters[$caster['id']]['Stats']['magicalAttackModifier'] * $attack_status_modifier / $this->monsters[$target['id']]['Stats']['magicalDefenseModifier'] / $defender_status_modifier);
			}
		}elseif($skill_effect['effect'] == 'True Damage') {
			$amount = round($amount);
		}elseif($skill_effect['effect'] == 'Heal') {
			$amount = round($amount);
		}else{
			$amount = round($amount);
		}
		
		if(in_array($skill_effect['effect'], ['Physical Damage','Magical Damage','True Damage','Leech','Heal'])) {
			$amount = $this->amountModifiers($time, $caster, $amount, $skill_effect['effect']); 
		}
		return $amount;
	}
	
	private function amountModifiers($time, $caster, $amount, $effect) {
		//split modifier
		if(!empty($caster['Monster']['statuses']['split'])) {
			$amount = round($amount / pow(2, $caster['Monster']['statuses']['split']['stacks']));
		}
		if(!empty($caster['Monster']['statuses']['phoenix-reborn'])) {
			$amount = round($amount * 1.5);
		}
		if($effect != 'Heal') {
			floor($amount * (1 + 0.1 * floor($time / 15000)));
		}
		
		return $amount;
	}
	
	private function healingDampening($time, $amount) {
		return floor($amount * (1 - 1 / 5 * floor($time / 10000)));
	}
	
	private function directHit($time, &$action_message, $monster) {
		if(!empty($this->monsters[$monster['id']]['Monster']['statuses']['discharge'])) {
			$this->monsters[$monster['id']]['Monster']['statuses']['discharge']['stacks']++;
			if($this->monsters[$monster['id']]['Monster']['statuses']['discharge']['stacks'] >= 3) {
				$amount = 6;
				$skill_effect = [
					'effect' => 'Magical Damage',
					'amount_min' => $amount,
					'amount_max' => $amount,
					'targets' => 'All Enemies',
					'chance' => 100
				];
				$targets = $this->getTargets($monster, $skill_effect['targets']);
				$this->processEffect($time, $action_message, $monster, 'Discharge', 'Electric', $skill_effect, $targets);
				$this->monsters[$monster['id']]['Monster']['statuses']['discharge']['stacks'] = 0;	
			}
		}
	}
	
	private function applyHealOverTime($time, &$action_message, $skill_effect, $monster) {
		//see if already healing over time
		if(!empty($this->monsters[$monster['id']]['Monster']['buffs']['healing-over-time'])) {
			$this->monsters[$monster['id']]['Monster']['buffs']['healing-over-time']['stacks']++;
			$this->monsters[$monster['id']]['Monster']['buffs']['healing-over-time']['amount_min'] += $this->amountModifiers($time, $monster, $skill_effect['amount_min'], 'Heal');
			$this->monsters[$monster['id']]['Monster']['buffs']['healing-over-time']['amount_max'] += $this->amountModifiers($time, $monster, $skill_effect['amount_max'], 'Heal');
			$this->monsters[$monster['id']]['Monster']['buffs']['healing-over-time']['ends'] = $time + $skill_effect['duration'] * 1000;
			$this->monsters[$monster['id']]['Monster']['buffs']['healing-over-time']['next_tick'] = $time + 1000;
			$this->addActionMessage($action_message, 'buff_gained', $this->monsters[$monster['id']]['Monster']['name'].' gains a Heal Over Time('.$this->monsters[$monster['id']]['Monster']['buffs']['healing-over-time']['stacks'].').');
		}else{
			$this->addActionMessage($action_message, 'buff_gained', $this->monsters[$monster['id']]['Monster']['name'].' gains a Heal Over Time.');
			$this->monsters[$monster['id']]['Monster']['buffs']['healing-over-time'] = [
				'stacks' => 1,
				'amount_min' => $this->amountModifiers($time, $monster, $skill_effect['amount_min'], 'Heal'),
				'amount_max' => $this->amountModifiers($time, $monster, $skill_effect['amount_max'], 'Heal'),
				'ends' => $time + $skill_effect['duration'] * 1000,
				'next_tick' => $time + 1000
			];
		}
	}
	
	private function applyPoison($time, &$action_message, $skill_effect, $monster) {
		//see if already poisoned
		if(!empty($this->monsters[$monster['id']]['Monster']['debuffs']['poisoned'])) {
			$this->monsters[$monster['id']]['Monster']['debuffs']['poisoned']['stacks']++;
			$this->monsters[$monster['id']]['Monster']['debuffs']['poisoned']['amount_min'] += $this->amountModifiers($time, $monster, $skill_effect['amount_min'], 'Magical Damage');
			$this->monsters[$monster['id']]['Monster']['debuffs']['poisoned']['amount_max'] += $this->amountModifiers($time, $monster, $skill_effect['amount_max'], 'Magical Damage');
			if($this->monsters[$monster['id']]['Monster']['debuffs']['poisoned']['ends'] < $time + $skill_effect['duration'] * 1000) {
				$this->monsters[$monster['id']]['Monster']['debuffs']['poisoned']['ends'] = $time + $skill_effect['duration'] * 1000;
				$this->monsters[$monster['id']]['Monster']['debuffs']['poisoned']['next_tick'] = $time + 1000;
			}
			$this->addActionMessage($action_message, 'debuff_gained', $this->monsters[$monster['id']]['Monster']['name'].' is Poisoned('.$this->monsters[$monster['id']]['Monster']['debuffs']['poisoned']['stacks'].').');
		}else{
			$this->addActionMessage($action_message, 'debuff_gained', $this->monsters[$monster['id']]['Monster']['name'].' is Poisoned.');
			$this->monsters[$monster['id']]['Monster']['debuffs']['poisoned'] = [
				'stacks' => 1,
				'amount_min' => $this->amountModifiers($time, $monster, $skill_effect['amount_min'], 'Magical Damage'),
				'amount_max' => $this->amountModifiers($time, $monster, $skill_effect['amount_max'], 'Magical Damage'),
				'ends' => $time + $skill_effect['duration'] * 1000,
				'next_tick' => $time + 1000
			];
		}
	}
	
	private function applyInfect($time, &$action_message, $skill_effect, $monster) {
		//see if already infected
		if(!empty($this->monsters[$monster['id']]['Monster']['debuffs']['infected'])) {
			$this->monsters[$monster['id']]['Monster']['debuffs']['infected']['stacks'] += rand($skill_effect['amount_min'], $skill_effect['amount_max']);
			$this->addActionMessage($action_message, 'debuff_gained', $this->monsters[$monster['id']]['Monster']['name'].' is Infected('.$this->monsters[$monster['id']]['Monster']['debuffs']['infected']['stacks'].').');
		}else{
			$this->monsters[$monster['id']]['Monster']['debuffs']['infected'] = [
				'stacks' => rand($skill_effect['amount_min'], $skill_effect['amount_max'])
			];
			if($this->monsters[$monster['id']]['Monster']['debuffs']['infected']['stacks'] == 1) {
				$this->addActionMessage($action_message, 'debuff_gained', $this->monsters[$monster['id']]['Monster']['name'].' is Infected.');
			}else{
				$this->addActionMessage($action_message, 'debuff_gained', $this->monsters[$monster['id']]['Monster']['name'].' is Infected('.$this->monsters[$monster['id']]['Monster']['debuffs']['infected']['stacks'].').');
			}
		}
	}
	
	private function useInfect($time, &$action_message, $monster) {
		if(!empty($this->monsters[$monster['id']]['Monster']['debuffs']['infected'])) {
			$this->monsters[$monster['id']]['Monster']['debuffs']['infected']['stacks']--;
			if($this->monsters[$monster['id']]['Monster']['debuffs']['infected']['stacks'] == 0) {
				unset($this->monsters[$monster['id']]['Monster']['debuffs']['infected']);
				$this->addActionMessage($action_message, 'debuff_lost', $this->monsters[$monster['id']]['Monster']['name'].' is no longer Infected.');
			}
		}
	}
	
	private function applyBubble($time, &$action_message, $skill_effect, $monster) {
		if(empty($this->monsters[$monster['id']]['Monster']['buffs']['bubble'])) {
			$this->addActionMessage($action_message, 'buff_lost', $this->monsters[$monster['id']]['Monster']['name'].' is protected in a Bubble.');
			$this->monsters[$monster['id']]['Monster']['buffs']['bubble'] = true;
		}
	}
	
	private function checkForInterrupt($time, &$action_message, $monster) {
		if(!empty($this->monsters[$monster['id']]['Monster']['next_use_skill']) && empty($this->monsters[$monster['id']]['Monster']['next_use_skill']['ultimate'])) {
			$this->addActionMessage($action_message, 'skill_result', $this->monsters[$monster['id']]['Monster']['next_use_skill']['name'].' was interrupted.');
			$this->tickStatuses($time, $action_message, $monster);
			$this->monsters[$monster['id']]['Monster']['next_action_skill']++;
			$this->monsters[$monster['id']]['Monster']['next_use_skill'] = null;
			$this->action_log[$time]['casting']['monster-'.$this->monsters[$monster['id']]['id']] = [
				'status' => 'interrupted'
			];
		}	
	}
	
	private function applyStun($time, &$action_message, $skill_effect, $monster) {
		$this->monsters[$monster['id']]['Monster']['next_action_time'] = $time + round($skill_effect['duration'] * 1000);
		if($skill_effect['duration'] > 0) {
			$this->addActionMessage($action_message, 'debuff_gained', $this->monsters[$monster['id']]['Monster']['name'].' is Stunned.');
			$this->monsters[$monster['id']]['Monster']['debuffs']['stunned']['ends'] = $this->monsters[$monster['id']]['Monster']['next_action_time'];
		}
		$this->checkForInterrupt($time, $action_message, $monster);
	}
	
	private function applySleep($time, &$action_message, $skill_effect, $monster) {
		$this->addActionMessage($action_message, 'debuff_gained', $this->monsters[$monster['id']]['Monster']['name'].' fell Asleep.');
		$this->checkForInterrupt($time, $action_message, $monster);
		$this->monsters[$monster['id']]['Monster']['next_action_time'] = $time + round($skill_effect['duration'] * 1000);
		$this->monsters[$monster['id']]['Monster']['debuffs']['asleep']['ends'] = $this->monsters[$monster['id']]['Monster']['next_action_time'];
	}
	
	private function applyFrozen($time, &$action_message, $skill_effect, $monster) {
		$this->addActionMessage($action_message, 'debuff_gained', $this->monsters[$monster['id']]['Monster']['name'].' is Frozen Solid.');
		$this->checkForInterrupt($time, $action_message, $monster);
		$this->monsters[$monster['id']]['Monster']['next_action_time'] = $time + round($skill_effect['duration'] * 1000);
		$this->monsters[$monster['id']]['Monster']['debuffs']['frozen']['ends'] = $this->monsters[$monster['id']]['Monster']['next_action_time'];
	}
	private function applyConfuse($time, &$action_message, $skill_effect, $monster) {
		$this->addActionMessage($action_message, 'debuff_gained', $this->monsters[$monster['id']]['Monster']['name'].' became Confused.');
		$this->monsters[$monster['id']]['Monster']['debuffs']['confused']['ends'] = $time + round($skill_effect['duration'] * 1000);
	}
	
	private function applyWet($time, &$action_message, $monster, $desired_wetness = null) {
		if(!empty($this->monsters[$monster['id']]['Monster']['statuses']['drenched'])) {
			$this->monsters[$monster['id']]['Monster']['statuses']['drenched']['ends'] = $time + WETNESS_DURATION;
			$this->addActionMessage($action_message, 'debuff_gained', $this->monsters[$monster['id']]['Monster']['name'].' continues to be Drenched in Water.');
		}elseif(!empty($this->monsters[$monster['id']]['Monster']['statuses']['soaked']) || (!empty($this->environment['status']['whirlpool']) && $desired_wetness == null) || $desired_wetness == 'Drenched') {
			if(!empty($this->monsters[$monster['id']]['Monster']['statuses']['soaked']))
				unset($this->monsters[$monster['id']]['Monster']['statuses']['soaked']);
			if(!empty($this->monsters[$monster['id']]['Monster']['statuses']['wet']))
				unset($this->monsters[$monster['id']]['Monster']['statuses']['wet']);
			$this->monsters[$monster['id']]['Monster']['statuses']['drenched'] = [
				'ends' => $time + WETNESS_DURATION
			];
			$this->addActionMessage($action_message, 'debuff_gained', $this->monsters[$monster['id']]['Monster']['name'].' is now Drenched.');
		}elseif(!empty($this->monsters[$monster['id']]['Monster']['statuses']['wet']) || $desired_wetness == 'Soaked') {
			unset($this->monsters[$monster['id']]['Monster']['statuses']['wet']);
			$this->monsters[$monster['id']]['Monster']['statuses']['soaked'] = [
				'ends' => $time + WETNESS_DURATION
			];
			$this->addActionMessage($action_message, 'debuff_gained', $this->monsters[$monster['id']]['Monster']['name'].' is now Soaked.');
		}else{
			$this->monsters[$monster['id']]['Monster']['statuses']['wet'] = [
				'ends' => $time + WETNESS_DURATION
			];
			$this->addActionMessage($action_message, 'debuff_gained', $this->monsters[$monster['id']]['Monster']['name'].' is now Wet.');
		}
	}
	
	private function applyUndying($time, &$action_message, $skill_effect, $monster) {
		if(empty($this->monsters[$monster['id']]['Monster']['buffs']['undying'])) {
			$this->addActionMessage($action_message, 'buff_gained', $this->monsters[$monster['id']]['Monster']['name'].' is Immortal for '.$skill_effect['duration'].' turn'.($skill_effect['duration'] == 1 ? '' : 's').'.');
			$this->monsters[$monster['id']]['Monster']['buffs']['undying'] = [
				'number_of_turns' => $skill_effect['duration']
			];
		}elseif($this->monsters[$monster['id']]['Monster']['buffs']['undying']['number_of_turns'] < $skill_effect['duration']) {
			$this->monsters[$monster['id']]['Monster']['buffs']['undying']['number_of_turns'] = $skill_effect['duration'];
		}
	}
	
	private function applyBurn($time, &$action_message, $skill_effect, $monster) {
		if(empty($this->monsters[$monster['id']]['Monster']['debuffs']['burned'])) {
			$this->addActionMessage($action_message, 'debuff_gained', $this->monsters[$monster['id']]['Monster']['name'].' is Burned.');
			$this->monsters[$monster['id']]['Monster']['debuffs']['burned'] = [
				'ends' => $time + BURN_DURATION
			];
		}elseif($this->monsters[$monster['id']]['Monster']['debuffs']['burned']['ends'] < $time + BURN_DURATION) {
			$this->monsters[$monster['id']]['Monster']['debuffs']['burned']['ends'] = $time + BURN_DURATION;
		}
	}
	
	private function triggerBurn($time, &$action_message, $monster) {
		if(!empty($this->monsters[$monster['id']]['Monster']['debuffs']['burned'])) {
			$this->takeDamage($time, $action_message, $monster, $monster, 'Burn', BURN_AMOUNT, false, 'burn_damage');
		}
	}
	
	private function applyDelay($time, &$action_message, $skill_effect, $monster) {
		$this->addActionMessage($action_message, 'debuff_gained', $this->monsters[$monster['id']]['Monster']['name'].' stutters.');
		$amount = rand($skill_effect['amount_min'] * 1000, $skill_effect['amount_max'] * 1000) / 1000;
		$this->monsters[$monster['id']]['Monster']['next_action_time'] += $amount;
	}
	
	private function createStats($monster) {
		return [
			'physicalAttackModifier' => 1 + 0.05 * ($this->monsters[$monster['id']]['Monster']['strength'] - 5),
			'physicalDefenseModifier' => 1 + 0.025 * ($this->monsters[$monster['id']]['Monster']['strength'] - 5),
			'evadeModifier' => 0.01 + 0.01 * ($this->monsters[$monster['id']]['Monster']['agility'] - 0) + 0.01 * ($this->monsters[$monster['id']]['Monster']['dexterity'] - 0),
			'speed' => 1.0 + 0.03 * ($this->monsters[$monster['id']]['Monster']['agility'] - 1),
			'hitModifier' => 0.01 + 0.01 * ($this->monsters[$monster['id']]['Monster']['dexterity'] - 0),
			'magicalAttackModifier' => 1 + 0.05 * ($this->monsters[$monster['id']]['Monster']['intelligence'] - 5),
			'magicalDefenseModifier' => 1 + 0.025 * ($this->monsters[$monster['id']]['Monster']['intelligence'] - 5),
			'criticalChance' => 0.01 + 0.02 * ($this->monsters[$monster['id']]['Monster']['luck'] - 1),
			'rerollChance' => 0.00 + 0.03 * ($this->monsters[$monster['id']]['Monster']['luck'] - 1),
			'health' => 98.0 + 2.0 * ($this->monsters[$monster['id']]['Monster']['vitality'] - 0)
		];
	}
	

	private function phoenixEgg() {
		$egg = [
			'Type' => [
				'name' => 'Fire'
			],
			'Monster' => [
				'name' => 'Phoenix Egg',
				'current_health' => 25,
				'max_health' => 25,
				'statuses' => [],
				'debuffs' => [],
				'buffs' => [],
				'strength' => 1,
				'agility' => 1,
				'dexterity' => 1,
				'intelligence' => 1,
				'luck' => 1,
				'vitality' => 1,
			],
			'cc_count' => -1,
			'skills' => [
				[
					'id' => 'Phoenix Rebirth',
					'Type' => [
						'name' => 'Fire'
					],
					'ultimate' => 1,
					'name' => 'Rebirth',
					'starting_charges' => 0,
					'charges_needed' => 0,
					'down_time' => 0,
					'cast_time' => 6,
					'down_time' => 0,
					'SkillEffect' => []
				]
			]
		];
		return $egg;	
	}

	private function randomTotem(&$action_message) {
		$rand = rand(1,2);
		if($rand == 1) {
			$totem = [
				'Type' => [
					'name' => 'Fire'
				],
				'Monster' => [
					'name' => 'Searing Totem',
					'current_health' => 6,
					'max_health' => 6,
					'statuses' => [],
					'debuffs' => [],
					'buffs' => [],
					'strength' => 1,
					'agility' => 1,
					'dexterity' => 1,
					'intelligence' => 1,
					'luck' => 1,
					'vitality' => 1,
				],
				'cc_count' => 0,
				'skills' => [
					[
						'Type' => [
							'name' => 'Fire'
						],
						'name' => 'Sear',
						'cast_time' => 1,
						'down_time' => 0,
						'SkillEffect' => [
							[
								'effect' => 'Magical Damage',
								'amount_min' => 1,
								'amount_max' => 1,
								'duration' => 0,
								'chance' => 100,
								'targets' => 'Single Enemy',
								'SecondarySkillEffect' => []
							]
						]
					]
				]
			];
			$action_message[] = [
				'type' => 'skill_result',
				'text' => 'Searing Totem is Summoned.'
			];
			return $totem;	
		}elseif($rand == 2) {
			
			$totem = [
				'Type' => [
					'name' => 'Water'
				],
				'Monster' => [
					'name' => 'Healing Totem',
					'current_health' => 6,
					'max_health' => 6,
					'statuses' => [],
					'debuffs' => [],
					'buffs' => [],
					'strength' => 1,
					'agility' => 1,
					'dexterity' => 1,
					'intelligence' => 1,
					'luck' => 1,
					'vitality' => 1,
				],
				'cc_count' => 0,
				'skills' => [
					[
						'Type' => [
							'name' => 'Water'
						],
						'name' => 'Healing Aura',
						'cast_time' => 1,
						'down_time' => 0,
						'SkillEffect' => [
							[
								'effect' => 'Heal',
								'amount_min' => 1,
								'amount_max' => 1,
								'duration' => 0,
								'chance' => 100,
								'targets' => 'All Allies',
								'SecondarySkillEffect' => []
							]
						]
					]
				]
			];
			$action_message[] = [
				'type' => 'skill_result',
				'text' => 'Healing Totem is Summoned.'
			];
			return $totem;	
		}
	}

	private function doomsayer() {
		$doomsayer = [
			'Type' => [
				'name' => 'Undead'
			],
			'Monster' => [
				'name' => 'Doomsayer',
				'current_health' => 1,
				'max_health' => 1,
				'statuses' => [],
				'debuffs' => [],
				'buffs' => [],
				'strength' => 1,
				'agility' => 1,
				'dexterity' => 1,
				'intelligence' => 1,
				'luck' => 1,
				'vitality' => 1,
			],
			'cc_count' => 0,
			'skills' => [
				[
					'id' => 'End of the World',
					'Type' => [
						'name' => 'Undead'
					],
					'ultimate' => 1,
					'name' => 'End of the World',
					'starting_charges' => 0,
					'charges_needed' => 0,
					'down_time' => 0,
					'cast_time' => 10,
					'down_time' => 0,
					'SkillEffect' => []
				]
			]
		];
		return $doomsayer;	
		
	}
	private function living_flesh($health) {
		$living_flesh = [
			'Type' => [
				'name' => 'Undead'
			],
			'Monster' => [
				'name' => 'Living Flesh',
				'current_health' => $health,
				'max_health' => $health,
				'statuses' => [],
				'debuffs' => [],
				'buffs' => [],
				'strength' => 1,
				'agility' => 1,
				'dexterity' => 1,
				'intelligence' => 1,
				'luck' => 1,
				'vitality' => 1,
			],
			'cc_count' => 0,
			'skills' => [
				[
					'id' => 'Poke',
					'Type' => [
						'name' => 'Undead'
					],
					'ultimate' => 0,
					'name' => 'Poke',
					'starting_charges' => 0,
					'charges_needed' => 0,
					'down_time' => 0,
					'cast_time' => 1,
					'down_time' => 0,
					'SkillEffect' => [
						[
							'effect' => 'Physical Damage',
							'amount_min' => 1,
							'amount_max' => 2,
							'duration' => 0,
							'chance' => 100,
							'targets' => 'Single Enemy',
							'SecondarySkillEffect' => []
						]
					]
				]
			]
		];
		return $living_flesh;	
		
	}


	private function wildEffects() {
		$goodTargets = [
			'Self',
			'Self',
			'Self',
			'Self',
			'Self',
			'Self',
			'Single Enemy',
			'All Enemies'
		];
		$badTargets = [
			'Self',
			'Single Enemy',
			'All Enemies',
			'Single Enemy',
			'All Enemies',
			'Single Enemy',
			'All Enemies'
		];
		$wildEffects =  [
			'Physical Damage' => [
				'amount_min' => 4,
				'amount_max' => 12,
				'duration' => 0,
				'targets' => $badTargets
			],
			'Magical Damage' => [
				'amount_min' => 4,
				'amount_max' => 12,
				'duration' => 0,
				'targets' => $badTargets
			],
			'True Damage' => [
				'amount_min' => 4,
				'amount_max' => 12,
				'duration' => 0,
				'targets' => $badTargets
			],
			'Leech' => [
				'amount_min' => 2,
				'amount_max' => 6,
				'duration' => 0,
				'targets' => $badTargets
			],
			'Heal' => [
				'amount_min' => 4,
				'amount_max' => 12,
				'duration' => 0,
				'targets' => $goodTargets
			],
			'Heal Over Time' => [
				'amount_min' => 2,
				'amount_max' => 4,
				'duration' => 3,
				'targets' => $goodTargets
			],
			'Attack Up' => [
				'amount_min' => 10,
				'amount_max' => 100,
				'duration' => 4,
				'targets' => $goodTargets
			],
			'Attack Down' => [
				'amount_min' => 10,
				'amount_max' => 100,
				'duration' => 4,
				'targets' => $badTargets
			],
			'Defense Up' => [
				'amount_min' => 10,
				'amount_max' => 100,
				'duration' => 4,
				'targets' => $goodTargets
			],
			'Defense Down' => [
				'amount_min' => 10,
				'amount_max' => 50,
				'duration' => 4,
				'targets' => $badTargets
			],
			'Speed Up' => [
				'amount_min' => 10,
				'amount_max' => 100,
				'duration' => 4,
				'targets' => $goodTargets
			],
			'Speed Down' => [
				'amount_min' => 10,
				'amount_max' => 50,
				'duration' => 4,
				'targets' => $badTargets
			],
			'Evade Up' => [
				'amount_min' => 10,
				'amount_max' => 100,
				'duration' => 4,
				'targets' => $goodTargets
			],
			'Evade Down' => [
				'amount_min' => 10,
				'amount_max' => 50,
				'duration' => 4,
				'targets' => $badTargets
			],
			'Bubble' => [
				'amount_min' => 0,
				'amount_max' => 0,
				'duration' => 0,
				'targets' => $goodTargets
			],
			'Stun' => [
				'amount_min' => 0,
				'amount_max' => 0,
				'duration' => 1,
				'targets' => $badTargets
			],
			'Sleep' => [
				'amount_min' => 0,
				'amount_max' => 0,
				'duration' => 1,
				'targets' => $badTargets
			],
			'Burn' => [
				'amount_min' => 0,
				'amount_max' => 0,
				'duration' => 0,
				'targets' => $badTargets
			],
			'Freeze' => [
				'amount_min' => 0,
				'amount_max' => 0,
				'duration' => 2,
				'targets' => $badTargets
			],
			'Poison' => [
				'amount_min' => 2,
				'amount_max' => 3,
				'duration' => 3,
				'targets' => $badTargets
			],
			'Infect' => [
				'amount_min' => 1,
				'amount_max' => 3,
				'duration' => 0,
				'targets' => $badTargets
			],
			'Undying' => [
				'amount_min' => 0,
				'amount_max' => 0,
				'duration' => 4,
				'targets' => $goodTargets
			]
		];
		return $wildEffects;
	}

}

?>