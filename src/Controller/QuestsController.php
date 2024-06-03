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
use Cake\I18n\DateTime;

/**
 * Static content controller
 *
 * This controller will render views from templates/Pages/
 *
 * @link https://book.cakephp.org/4/en/controllers/pages-controller.html
 */
class QuestsController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authentication->addUnauthenticatedActions([]);

        $this->loadComponent('Combat');
    }

    public function index() {
        $user_id = $this->user->id;
        $completed_quests = $this->Quests
            ->find()
            ->matching('Users', function ($q) use ($user_id) {
                return $q->where([
					'QuestsUsers.user_id' => $user_id,
					'QuestsUsers.completed IS NOT NULL'
				]);
            })
            ->contain([
                'QuestMonsters',
                'QuestRewards' => [
                    'Skills',
                    'Ultimates',
                    'Types',
                    'SecondaryTypes'
                ]
            ])
            ->contain('UserQuestRewards', function (SelectQuery $q) use ($user_id) {
                return $q
                    ->where([
                        'UserQuestRewards.user_id' => $user_id
                    ])
                    ->contain([
                        'Skills',
                        'Quests',
                        'Ultimates',
                        'Types',
                        'SecondaryTypes'
                    ]);
            })
            ->order([
                'QuestsUsers.completed DESC'
            ])
            ->all()
            ->toList();
        $completed_quest_ids = [1];
        foreach($completed_quests as $index=>$quest) {
            if(!empty($quest->_matchingData['QuestsUsers']->monster_id)) {
                $completed_quests[$index]->monster = $this->fetchTable('Monsters')
                    ->find()
                    ->where([
                        'Monsters.id' => 1
                    ])
                    ->first();
            } 
            $completed_quest_ids[] = $quest->id;
        }
        $available_quests = $this->Quests
            ->find()
            ->notMatching('Users', function ($q) use ($user_id) {
                return $q->where([
					'QuestsUsers.user_id' => $user_id,
					'QuestsUsers.completed IS NOT NULL',
				]);
            })
            ->matching('ParentQuests', function ($q) use ($completed_quest_ids) {
                return $q->where([
                    'ParentQuests.id IN' => $completed_quest_ids

                ]);
            })
            ->contain([
                'QuestMonsters',
                'QuestRewards' => [
                    'Skills',
                    'Ultimates',
                    'Types',
                    'SecondaryTypes'
                ]
            ])
            ->all();
        
        $available_monsters = $this->fetchTable('Monsters')
			->find('list')
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
			->all();
        $this->set(compact('completed_quests','available_quests','available_monsters'));
    }

    public function view($quest_id) {
        $user_id = $this->user->id;
        $completed_quests = $this->Quests
            ->find()
            ->matching('Users', function ($q) use ($user_id) {
                return $q->where([
					'QuestsUsers.user_id' => $user_id,
					'QuestsUsers.completed IS NOT NULL'
				]);
            })
            ->contain('UserQuestRewards', function (SelectQuery $q) use ($user_id) {
                return $q
                    ->where([
                        'UserQuestRewards.user_id' => $user_id
                    ]);
            })
            ->all()
            ->toList();
        $completed_quest_ids = [1];
        foreach($completed_quests as $index=>$quest) {
            if(!empty($quest->_matchingData['QuestsUsers']->monster_id)) {
                $completed_quests[$index]->monster = $this->fetchTable('Monsters')
                    ->find()
                    ->where([
                        'Monsters.id' => 1
                    ])
                    ->first();
            } 
            $completed_quest_ids[] = $quest->id;
        }
        $quest = $this->Quests
            ->find()
            ->notMatching('Users', function ($q) use ($user_id) {
                return $q->where([
					'QuestsUsers.user_id' => $user_id,
					'QuestsUsers.completed IS NOT NULL',
				]);
            })
            ->matching('ParentQuests', function ($q) use ($completed_quest_ids) {
                return $q->where([
                    'ParentQuests.id IN' => $completed_quest_ids

                ]);
            })
            ->where([
                'Quests.id' => $quest_id
            ])
            ->contain([
                'QuestRewards' => [
                    'Skills',
                    'Ultimates',
                    'Types',
                    'SecondaryTypes'
                ]
            ])
            ->contain('QuestMonsters', function (SelectQuery $q) {
                return $q
                    ->find('forBattle');
            })
            ->first();
        
		if ($this->request->is('post','put')) {
            $monster = $this->fetchTable('Monsters')
                ->find('forBattle')
                ->where([
                    'Monsters.id' => $this->request->getData()['monster_id'],
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
                ->firstOrFail();
            $opponents = [];
            foreach($quest->quest_monsters  as $quest_monster) {
                if($quest_monster->clone) {
                    $clone_monster = clone $monster;
                    $clone_monster->name = 'Clone of '.$monster->name;
                    $clone_monster->skill1 = clone $monster->skill1;
                    $clone_monster->skill2 = clone $monster->skill2;
                    $clone_monster->skill3 = clone $monster->skill3;
                    $clone_monster->skill4 = clone $monster->skill4;
                    $clone_monster->ultimate = clone $monster->ultimate;
                    unset($clone_monster->rune1);
                    unset($clone_monster->rune2);
                    unset($clone_monster->rune3);
                    $opponents[] = $clone_monster;
                }else{
                    $opponents[] = $quest_monster;
                }
            }
            
            $result = $this->Combat->twoTeamCombat([clone $monster], $opponents);
            $quests_user = $this->Quests->QuestsUsers
                ->find()
                ->where([
                    'QuestsUsers.quest_id' => $quest->id,
                    'QuestsUsers.user_id' => $this->user->id
                ])
                ->first();
            if(empty($quests_user)) {
                $quests_user = $this->Quests->QuestsUsers->newEntity([
                    'quest_id' => $quest->id,
                    'user_id' => $this->user->id,
                    'monster_id' => $monster->id
                ]);
            }
            $quests_user->result_json_data = json_encode([$result],JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
            if($result['winning_id'] == 1) {
                $quests_user->completed = new DateTime();
                //grant_rewards
                foreach($quest->quest_rewards as $quest_reward) {
                    $this->Quests->QuestRewards->grantRewardToUser($quest_reward, $this->user);
                }
            }
            $this->Quests->QuestsUsers->save($quests_user);
            if($quest->required_rest > 0) {
                $monster->resting_until = new DateTime('+'.$quest->required_rest.' minutes');
                $monster = $this->fetchTable('Monsters')->save($monster);
            }
			return $this->redirect(array('action' => 'battle',$quest->id));
        }
        $available_monsters = $this->fetchTable('Monsters')
			->find('list')
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
			->all();
        $this->set(compact('quest','available_monsters'));

    }

    public function battle($quest_id) {
        $user_id = $this->user->id;
        $quest = $this->Quests
            ->find()
            ->where([
                'Quests.id' => $quest_id
            ])
            ->matching('Users', function ($q) use ($user_id) {
                return $q->where([
					'QuestsUsers.user_id' => $user_id,
				]);
            })
            ->contain('UserQuestRewards', function ($q) use ($user_id) {
                return $q->where([
					'UserQuestRewards.user_id' => $user_id,
				])
                ->contain([
                    'Skills',
                    'Ultimates',
                    'Types',
                    'SecondaryTypes'
                ]);
            })
            ->firstOrFail();
        $this->set('quest', $quest);
		$this->set('battlesJSON',$quest->_matchingData['QuestsUsers']->result_json_data);
		$this->set('statuses',$this->fetchTable('Statuses')
            ->find()
            ->all()
            ->toList());
	}
}