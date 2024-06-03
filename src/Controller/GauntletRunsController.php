<?php
declare(strict_types=1);
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\View\Exception\MissingTemplateException;
use Cake\Event\EventInterface;
use Cake\I18n\DateTime;

/**
 * Static content controller
 *
 * This controller will render views from templates/Pages/
 *
 * @link https://book.cakephp.org/4/en/controllers/pages-controller.html
 */
class GauntletRunsController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authentication->addUnauthenticatedActions([]);

        $this->loadComponent('Combat');
    }

	public function index() {
		$gauntlet_runs = $this->paginate($this->GauntletRuns
            ->find()
            ->where(['GauntletRuns.user_id' => $this->user->id])
            ->contain([
                'Monsters' => [
					'Skill1',
					'Skill2',
					'Skill3',
					'Skill4',
					'Ultimates',
					'Rune1' => [
						'Types'
					],
					'Rune2' => [
						'Types'
					],
					'Rune3' => [
						'Types'
					]
				]
            ])
			->order([
				'GauntletRuns.created DESC'
			])
		);

        
        $available_monsters = $this->fetchTable('Monsters')
			->find('forBattle')
			->where([
				'Monsters.user_id' => $this->user->id,
				'Monsters.in_gauntlet_run' => 0,
                'OR' => [
				    'Monsters.resting_until IS NULL',
				    'Monsters.resting_until <=' => date('Y-m-d H:i:s')
                ],
				'Monsters.skill_1_id != 0',
				'Monsters.skill_2_id != 0',
				'Monsters.skill_3_id != 0',
				'Monsters.skill_4_id != 0',
				'Monsters.ultimate_id != 0'
			])
			->order([
				'Monsters.elo_rating DESC'
			])
			->all();
		$available_monsters_list = [];
		foreach($available_monsters as $monster) {
			$available_monsters_list[$monster->id] = $monster->name.' | Using: '.$monster->get('listOfAbilities').' | Rating: '.$monster->elo_rating;
		}
		$monsters_in_gauntlet_run = $this->fetchTable('Monsters')
			->find()
			->where([
				'Monsters.user_id' => $this->user->id,
				'Monsters.in_gauntlet_run' => 1
			])
			->all();
        $this->set(compact('gauntlet_runs','available_monsters_list','monsters_in_gauntlet_run'));
	}
	
	public function completed() {
        $this->set('gauntlet_runs', $this->paginate($this->GauntletRuns
            ->find()
            ->where(['GauntletRuns.user_id' => $this->user->id])
            ->contain([
                'Monsters' => [
					'Skill1',
					'Skill2',
					'Skill3',
					'Skill4',
					'Ultimates',
					'Rune1' => [
						'Types'
					],
					'Rune2' => [
						'Types'
					],
					'Rune3' => [
						'Types'
					]
				]
            ])
			->order([
				'GauntletRuns.created DESC'
			])
		));
	}
	
	public function startRun($monster_id = null) {
		
		// if(!empty($this->user->dreaming_since)) {
		// 	$this->Flash->error(__('You can not start a gauntlet run while in Dream Mode.'));
		// 	return $this->redirect(['action' => 'index']);
		// }
		if($this->user->total_gauntlet_runs_today >= $this->user->active_monster_limit * DAILY_GAUNTLET_LIMIT_PER_ACTIVE_MONSTER) {
			$this->Flash->error(__('You can not start any more gauntlet runs today.'));
			return $this->redirect(['action' => 'index']);
		}
		
		if(count($this->user->monsters) >= $this->user->active_monster_limit) {
			$this->Flash->error(__('You can only have '.$this->user->active_monster_limit.' Monster'.($this->user->active_monster_limit == 1 ? '' : 's').' active in the Gauntlet at a time.'));
			return $this->redirect(['action' => 'index']);
		}
		if(empty($monster_id))
			$monster_id = $this->request->getData()['monster_id'];
		
		$monster = $this->GauntletRuns->Monsters
			->find()
			->where([
				'Monsters.id' => $monster_id,
				'Monsters.user_id' => $this->user->id,
				'Monsters.in_gauntlet_run' => 0,
				'Monsters.skill_1_id != 0',
				'Monsters.skill_2_id != 0',
				'Monsters.skill_3_id != 0',
				'Monsters.skill_4_id != 0',
				'Monsters.ultimate_id != 0'
			])
			->first();
		if (empty($monster->id)) {
			throw new NotFoundException(__('Invalid monster'));
		}
		$monster->in_gauntlet_run = 1;
		if($monster->new) {
			$monster->new = 0;
			$monster->in_gauntlet_run_until = new DateTime();
		$this->Flash->success(__($monster->name.' has started battling in the Gauntlet. Because it was it\'s first run, it immediately completed it. Future runs will take '.GAUNTLET_WAIT_TIME.'.'));
		}else{
			$monster->in_gauntlet_run_until = new DateTime(GAUNTLET_WAIT_TIME);
		$this->Flash->success(__($monster->name.' has started battling in the Gauntlet. It will be done at '.$monster->in_gauntlet_run_until->format('g:ia').' PST'));
		}
		$this->GauntletRuns->Monsters->save($monster);
		return $this->redirect(['action' => 'index']);
	}
	
	public function completeRun($monster_id = null) {
		$monster = $this->GauntletRuns->Monsters
			->find('forBattle')
			->where([
				'Monsters.id' => $monster_id,
				'Monsters.user_id' => $this->user->id
			])
			->first();
		if (empty($monster->id)) {
			throw new NotFoundException(__('Invalid monster'));
		}elseif($monster->in_gauntlet_run_until && (int)$monster->in_gauntlet_run_until->toUnixString() > time()) {
			$this->Flash->error(__('Monster is not finished in the Gauntlet yet.'));
			return $this->redirect(['action' => 'index']);
		}elseif(!$monster->in_gauntlet_run) {
			$this->Flash->error(__('Monster wasn\'t in the gauntlet.'));
			return $this->redirect(['action' => 'index']);
		}
		//create run
		$gauntlet_run = $this->GauntletRuns->newEntity([
			'user_id' => $this->user->id,
			'monster_id' => $monster_id,
			'skill_1_id' => $monster->skill_1_id,
			'skill_2_id' => $monster->skill_2_id,
			'skill_3_id' => $monster->skill_3_id,
			'skill_4_id' => $monster->skill_4_id,
			'ultimate_id' => $monster->ultimate_id,
			'rune_1_id' => $monster->rune_1_id,
			'rune_2_id' => $monster->rune_2_id,
			'rune_3_id' => $monster->rune_3_id,
		]);
		$this->GauntletRuns->save($gauntlet_run);
		$monster->in_gauntlet_run = 0;
		$monster->resting_until = new DateTime(GAUNTLET_REST_TIME);
		$this->GauntletRuns->Monsters->save($monster);
		
		$already_fought_ids = [
			$monster->id
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
			$opponent = null;
			$elo_threshold = 1;
			while(empty($opponent->id)) {
				$where = [
					'NOT' => [
						'Monsters.id IN' => $already_fought_ids
					],
					'Monsters.skill_1_id != 0',
					'Monsters.skill_2_id != 0',
					'Monsters.skill_3_id != 0',
					'Monsters.skill_4_id != 0',
					'Monsters.ultimate_id != 0'
				];
				if($elo_threshold < 15) {
					$where['Monsters.user_id !='] = $this->user->id;
				}
				if($elo_threshold < 10) {
					$elo_threshold_amount = $elo_threshold * ELO_CONSTANT;
					$where['Monsters.elo_rating >='] = $monster->elo_rating - $elo_threshold_amount;
					$where['Monsters.elo_rating <='] = $monster->elo_rating + $elo_threshold_amount;
				}
				$opponent = $this->fetchTable('Monsters')
					->find('forBattle')
					->where($where)
					->order([
						'RAND()'
					])
					->first();
				$elo_threshold++;
			}
			$already_fought_ids[] = $opponent->id;
			$result = $this->Combat->twoTeamCombat([clone $monster], [clone $opponent]);
			
			
			
			//ELO System
			$monster_q = pow(10, $monster->elo_rating / 400);
			$opponent_q = pow(10, $opponent->elo_rating / 400);
			
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
			$gauntlet_run_battle = $this->GauntletRuns->GauntletRunBattles->newEntity([
				'gauntlet_run_id' => $gauntlet_run->id,
				'user_id' => $this->user->id,
				'monster_id' => $monster->id,
				'opponent_id' => $opponent->id,
				'result' => $result_text,
				'monster_elo_rating' => $monster->elo_rating,
				'opponent_elo_rating' => $opponent->elo_rating,
				'result_json_data' => json_encode($result, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP),
				'order' => $battles
			]);
			$this->GauntletRuns->GauntletRunBattles->save($gauntlet_run_battle);
			
			//$firstMonster
			$ratingChange = round(ELO_CONSTANT * ($monster_s - $monster_e));
			$monster->elo_rating += $ratingChange;
			$elo_change += $ratingChange;
			$monster->total_battles += 1;
			$this->GauntletRuns->Monsters->save($monster);
			if($monster->total_battles > 20) {
				//$secondMonster
				$ratingChange = round(ELO_CONSTANT * ($opponent_s - $opponent_e));
				$opponent->elo_rating += $ratingChange;
				$this->GauntletRuns->Monsters->save($opponent);
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
		$gauntlet_run->wins = $wins;
		$gauntlet_run->losses = $losses;
		$gauntlet_run->ties = $ties;
		$gauntlet_run->longest_streak = $longest_streak;
		$gauntlet_run->hot_streaks = $hot_streaks;
		$gauntlet_run->number_of_reward_picks = $number_of_reward_picks;
		$gauntlet_run->number_of_reward_options = $number_of_reward_options;
		$gauntlet_run->elo_change = $elo_change;
		$this->GauntletRuns->save($gauntlet_run);
		
		$this->user->gold += 5 * $wins;
		$this->GauntletRuns->Users->save($this->user);
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
			$reward = $this->getReward($this->user->id, $rewards_currently, $guaranteed_rarity);
			if(!empty($reward)) {
				$guaranteed_rarity = null;
				$rewards_currently[] = $reward;
			}
			$attempts++;
		}
		foreach($rewards_currently as $reward) {
			$gauntlet_run_reward = $this->GauntletRuns->GauntletRunRewards->newEntity([
				'user_id' => $this->user->id,
				'gauntlet_run_id' => $gauntlet_run->id,
				'type' => $reward['type'],
				'rarity' => $reward['rarity']
			]);
			if($reward['type'] == 'Skill') {
				$gauntlet_run_reward->skill_id = $reward['skill_id'];
			}elseif($reward['type'] == 'Ultimate') {
				$gauntlet_run_reward->ultimate_id = $reward['ultimate_id'];
			}elseif($reward['type'] == 'Gems' || $reward['type'] == 'Gold' || $reward['type'] == 'Rune Shards') {
				$gauntlet_run_reward->amount = $reward['amount'];
			}
			$this->GauntletRuns->GauntletRunRewards->save($gauntlet_run_reward);
			$this->grantReward($gauntlet_run_reward);
		}
		return $this->redirect(['action' => 'view-results', $gauntlet_run->id]);
	}
	
	function viewBattles($gauntlet_run_id) {
		$gauntlet_run = $this->GauntletRuns
			->find()
			->where([
				'GauntletRuns.id' => $gauntlet_run_id
			])
			->contain([
				'Monsters',
				'GauntletRunBattles' => [
					'Opponents'
				]
			])
			->firstOrFail();
		if($gauntlet_run->user_id != $this->user->id && $this->user->type != 'Admin') {
			throw new NotFoundException(__('Invalid run'));
		}
		$gauntletRunJSON = [];
		foreach($gauntlet_run->gauntlet_run_battles as $gauntlet_run_battle) {
			$gauntletRunJSON[] = json_decode($gauntlet_run_battle->result_json_data, true);
		}
		$this->set('battlesJSON', json_encode($gauntletRunJSON, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP));
		$this->set('gauntlet_run', $gauntlet_run);
		$this->set('statuses',$this->fetchTable('Statuses')
			->find()
			->all()
		);
	}
	function viewResults($gauntlet_run_id) {
		$gauntlet_run = $this->GauntletRuns
			->find()
			->where([
				'GauntletRuns.id' => $gauntlet_run_id
			])
			->contain([
				'GauntletRunBattles' => [
					'Opponents' => [
						'Users'
					]
				],
				'GauntletRunRewards' => [
					'Skills' => [
						'Types'
					],
					'Ultimates' => [
						'Types'
					]
				],
				'Monsters'
			])
			->firstOrFail();
		if($gauntlet_run->user_id != $this->user->id && $this->user->type != 'Admin') {
			throw new NotFoundException(__('Invalid run'));
		}
		$this->set('gauntlet_run', $gauntlet_run);
	}
	
	private function getReward($user_id, &$rewards_currently, $at_least_rarity = null) {
		//get types for monsters
		$types = $this->fetchTable('Types')
			->find()
			->all()
			->toList();
		
		
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
		$dont_find_skills = [0];
		foreach($rewards_currently as $reward) {
			if($reward['type'] == 'Skill') {
				$dont_find_skills[$reward['skill_id']] = $reward['skill_id'];
			}
		}
		$user_id = $this->user->id;

		$skills = $this->fetchTable('Skills')
			->find('list')
			->where([
				'NOT' => [
					'Skills.id IN' => $dont_find_skills
				],
				'Skills.rarity' => $rarity
			])
            ->notMatching('UserSkills', function ($q) use ($user_id) {
                return $q->where(['UserSkills.user_id' => $user_id]);
            })
			->all();
		foreach($skills as $skill_id => $skill) {
			$options[] = [
				'type' => 'Skill',
				'skill_id' => $skill_id
			];
		}
		//find ultimates 
		$dont_find_ultimates = [0];
		foreach($rewards_currently as $reward) {
			if($reward['type'] == 'Ultimate') {
				$dont_find_ultimates[$reward['ultimate_id']] = $reward['ultimate_id'];
			}
		}
		$ultimates = $this->fetchTable('Ultimates')
			->find('list')
			->where([
				'NOT' => [
					'Ultimates.id IN' => $dont_find_ultimates
				],
				'Ultimates.rarity' => $rarity
			])
            ->notMatching('UserUltimates', function ($q) use ($user_id) {
                return $q->where(['UserUltimates.user_id' => $user_id]);
            })
			->all();
		foreach($ultimates as $ultimate_id => $ultimate) {
			$options[] = [
				'type' => 'Ultimate',
				'ultimate_id' => $ultimate_id
			];
		}
		//populate gems and rune shards based off how many other rewards there are
		$total_options = count($options);
		for($i=0; $i<max(1,round($total_options / 3)); $i++) {
			if($rarity == 'Legendary') {
				$options[] = [
					'type' => 'Gems',
					'amount' => 100
				];
				$options[] = [
					'type' => 'Rune Shards',
					'amount' => 5000
				];
			}elseif($rarity == 'Epic') {
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
				$options[] = [
					'type' => 'Rune Shards',
					'amount' => 250
				];
			}elseif($rarity == 'Uncommon') {
				$options[] = [
					'type' => 'Gems',
					'amount' => 1
				];
				$options[] = [
					'type' => 'Rune Shards',
					'amount' => 50
				];
			}elseif($rarity == 'Common') {
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
		if($gauntlet_run_reward->type == 'Skill') {
			$user_skill = $this->fetchTable('UserSkills')->newEntity([
				'user_id' => $gauntlet_run_reward->user_id,
				'skill_id' => $gauntlet_run_reward->skill_id
			]);
			$this->fetchTable('UserSkills')->save($user_skill);
		}elseif($gauntlet_run_reward->type == 'Ultimate') {
			$user_skill = $this->fetchTable('UserUltimates')->newEntity([
				'user_id' => $gauntlet_run_reward->user_id,
				'ultimate_id' => $gauntlet_run_reward->ultimate_id
			]);
			$this->fetchTable('UserUltimates')->save($user_skill);
		}else{
			if($gauntlet_run_reward->type == 'Gems') {
				$this->user->gems += $gauntlet_run_reward['amount'];
			}elseif($gauntlet_run_reward['type'] == 'Gold') {
				$this->user->gold += $gauntlet_run_reward['amount'];
			}elseif($gauntlet_run_reward['type'] == 'Rune Shards') {
				$this->user->rune_shards += $gauntlet_run_reward['amount'];
			}
			$this->GauntletRuns->Users->save($this->user);
		}
		return true;
	}
	
}