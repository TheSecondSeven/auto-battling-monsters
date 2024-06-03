<?php
declare(strict_types=1);
namespace App\Controller\Admin;

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
class QuestsController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authentication->addUnauthenticatedActions([]);
    }

    public function index() {
        $this->paginate = [
			'Quests' => [
				'order' => [
					'depth' => 'ASC',
					'required_rest' => 'ASC',
					'title' => 'ASC'
				],
			]
		];
        $quests = $this->paginate($this->Quests
            ->find()
            ->contain([
                'ParentQuests',
                'ChildQuests',
                'QuestMonsters',
                'QuestRewards'
            ])
        );
        $this->set(compact('quests'));
    }

    public function view($id = null) {
		$this->set('quest', $this->Quests
            ->find()
            ->where([
                'Quests.id' => $id
            ])
            ->contain([
                'ParentQuests',
                'ChildQuests',
                'QuestMonsters',
                'QuestRewards' => [
                    'Skills',
                    'Ultimates',
                    'Types',
                    'SecondaryTypes'
                ]
            ])
			->first()
        );
	}
    
	public function create($parent_quest_id = null) {
        $quest = $this->Quests->newEntity([
            'quest_id' => $parent_quest_id
        ]);
		if ($this->request->is('post')) {
            $quest = $this->Quests->patchEntity($quest, $this->request->getData());
			if ($this->Quests->save($quest)) {
				$this->Flash->success(__('The quest has been created.'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('The quest could not be saved. Please, try again.'));
			}
		}
        if(!empty($parent_quest_id)) {
            $parent_quest = $this->Quests
                ->find()
                ->where([
                    'Quests.id' => $parent_quest_id
                ])
                ->first();
        }
        $parent_quest = null;
		$this->set(compact('quest','parent_quest'));
	}
    
	public function update($id = null) {
		$quest = $this->Quests
            ->find()
            ->where([
                'Quests.id' => $id
            ])
            ->firstOrFail();
		if ($this->request->is(array('post', 'put'))) {
            $quest = $this->Quests->patchEntity($quest, $this->request->getData());
			if ($this->Quests->save($quest)) {
				$this->Flash->success(__('The quest has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The quest could not be saved. Please, try again.'));
			}
		}
		$this->set(compact('quest'));
	}

	public function delete($id = null) {
		$quest = $this->Quests
            ->find()
            ->where([
                'Quests.id' => $id
            ])
            ->firstOrFail();
		if ($this->Quests->delete($quest)) {
			$this->Flash->success(__('The quest has been deleted.'));
		} else {
			$this->Flash->error(__('The quest could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}


	public function addQuestReward($quest_id = null) {
		$quest = $this->Quests
            ->find()
            ->where([
                'Quests.id' => $quest_id
            ])
            ->firstOrFail();
        $quest_reward = $this->Quests->QuestRewards->newEntity(['quest_id' => $quest_id]);
		if ($this->request->is('post')) {
            $quest_reward = $this->Quests->QuestRewards->patchEntity($quest_reward, $this->request->getData());
			if ($this->Quests->QuestRewards->save($quest_reward)) {
				$this->Flash->success(__('The quest reward has been added.'));
				return $this->redirect(array('controller' => 'quests', 'action' => 'view', $quest_id));
			} else {
				$this->Flash->error(__('The quest reward could not be saved. Please, try again.'));
			}
		}
        $reward_types = $this->Quests->QuestRewards->reward_types();
		$types = ['' => 'None'] + $this->Quests->QuestRewards->Types->find('list')
            ->where([])
            ->all()
            ->toArray();
        $skills = ['' => 'None'] + $this->Quests->QuestRewards->Skills
            ->find('list')
            ->all()
            ->toArray();
        $ultimates = ['' => 'None'] + $this->Quests->QuestRewards->Ultimates
            ->find('list')
            ->all()
            ->toArray();
        $this->set(compact('quest_reward','reward_types','types','skills','ultimates'));
	}

	public function updateQuestReward($quest_id, $quest_reward_id) {
		$quest_reward = $this->Quests->QuestRewards
            ->find()
            ->where([
                'QuestRewards.id' => $quest_reward_id
            ])
            ->firstOrFail();
		if ($this->request->is(array('post', 'put'))) {
            $quest_reward = $this->Quests->QuestRewards->patchEntity($quest_reward, $this->request->getData());
			if ($this->Quests->QuestRewards->save($quest_reward)) {
				$this->Flash->success(__('The quest reward effect has been updated.'));
				return $this->redirect(array('controller' => 'quests', 'action' => 'view', $quest_id));
			} else {
				$this->Flash->error(__('The skill effect could not be saved. Please, try again.'));
			}
		}
        $reward_types = $this->Quests->QuestRewards->reward_types();
		$types = ['' => 'None'] + $this->Quests->QuestRewards->Types->find('list')
            ->where([])
            ->all()
            ->toArray();
        $skills = ['' => 'None'] + $this->Quests->QuestRewards->Skills
            ->find('list')
            ->all()
            ->toArray();
        $ultimates = ['' => 'None'] + $this->Quests->QuestRewards->Ultimates
            ->find('list')
            ->all()
            ->toArray();
        $this->set(compact('quest_reward','reward_types','types','skills','ultimates'));
	}

	public function deleteQuestReward($quest_id, $id = null) {
		$quest_reward = $this->Quests->QuestRewards
            ->find()
            ->where([
                'QuestRewards.id' => $id
            ])
            ->firstOrFail();
		if ($this->Quests->QuestRewards->delete($quest_reward)) {
			$this->Flash->success(__('The quest reward has been deleted.'));
		} else {
			$this->Flash->error(__('The quest reward could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'view', $quest_id));
	}

	public function addQuestMonster($quest_id = null) {
		$quest = $this->Quests
            ->find()
            ->where([
                'Quests.id' => $quest_id
            ])
            ->firstOrFail();
        $quest_monster = $this->Quests->QuestMonsters->newEntity(['quest_id' => $quest_id]);
		if ($this->request->is('post')) {
            $quest_monster = $this->Quests->QuestMonsters->patchEntity($quest_monster, $this->request->getData());
			if ($this->Quests->QuestMonsters->save($quest_monster)) {
				$this->Flash->success(__('The quest monster has been added.'));
				return $this->redirect(array('controller' => 'quests', 'action' => 'view', $quest_id));
			} else {
				$this->Flash->error(__('The quest monster could not be saved. Please, try again.'));
			}
		}
        $skills = ['' => 'None'] + $this->Quests->QuestMonsters->Skill1
            ->find('list')
            ->order([
                'Skill1.rarity = "Admin Only" DESC',
                'Skill1.name ASC'
            ])
            ->all()
            ->toArray();
        $ultimates = ['' => 'None'] + $this->Quests->QuestMonsters->Ultimates
            ->find('list')
            ->order([
                'Ultimates.rarity = "Admin Only" DESC',
                'Ultimates.name ASC'
            ])
            ->all()
            ->toArray();
        $this->set(compact('quest_monster','skills','ultimates'));
	}
	public function updateQuestMonster($quest_id, $quest_monster_id) {
		$quest_monster = $this->Quests->QuestMonsters
            ->find()
            ->where([
                'QuestMonsters.id' => $quest_monster_id
            ])
            ->firstOrFail();
		if ($this->request->is(array('post', 'put'))) {
            $quest_monster = $this->Quests->QuestMonsters->patchEntity($quest_monster, $this->request->getData());
			if ($this->Quests->QuestMonsters->save($quest_monster)) {
				$this->Flash->success(__('The quest monster has been updated.'));
				return $this->redirect(array('controller' => 'quests', 'action' => 'view', $quest_id));
			} else {
				$this->Flash->error(__('The quest monster could not be saved. Please, try again.'));
			}
		}
        $skills = ['' => 'None'] + $this->Quests->QuestMonsters->Skill1
            ->find('list')
            ->order([
                'Skill1.rarity = "Admin Only" DESC',
                'Skill1.name ASC'
            ])
            ->all()
            ->toArray();
        $ultimates = ['' => 'None'] + $this->Quests->QuestMonsters->Ultimates
            ->find('list')
            ->order([
                'Ultimates.rarity = "Admin Only" DESC',
                'Ultimates.name ASC'
            ])
            ->all()
            ->toArray();
        $this->set(compact('quest_monster','skills','ultimates'));
	}

	public function deleteQuestMonster($quest_id, $id = null) {
		$quest_monster = $this->Quests->QuestMonsters
            ->find()
            ->where([
                'QuestMonsters.id' => $id
            ])
            ->firstOrFail();
		if ($this->Quests->QuestMonsters->delete($quest_monster)) {
			$this->Flash->success(__('The quest monster has been deleted.'));
		} else {
			$this->Flash->error(__('The quest monster could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'view', $quest_id));
	}

}