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
class SkillsController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authentication->addUnauthenticatedActions([]);
    }

    public function index() {


        $rarity = $this->request->getQuery('rarity');
        $type_id = $this->request->getQuery('type_id');
        $name = $this->request->getQuery('name');
        $where = [];
        if(!empty($rarity)) $where['Skills.rarity'] = $rarity;
        if(!empty($type_id)) $where['Skills.type_id'] = $type_id;
        if(!empty($name)) $where['Skills.name LIKE'] = '%'.$name.'%';

		$this->paginate = [
			'Skills' => [
				'order' => [
					'type_id' => 'ASC',
					'rarity' => 'DESC',
					'value' => 'DESC'
				],
			]
		];
		$skills = $this->paginate($this->Skills
            ->find()
            ->where($where)
			->contain([
				'Types'
			])
        );
        $rarities = $this->Skills->rarities();

		$types = $this->Skills->Types->find('list')
            ->where([])
            ->all();
        $this->set(compact(['skills','rarities','types']));
	}


    public function mySkills() {
        $user_id = $this->user->id;
		$this->set('skills', $this->paginate($this->Skills
            ->find()
            ->matching('UserSkills', function ($q) use ($user_id) {
                return $q->where(['UserSkills.user_id' => $user_id]);
            })
            ->contain([
                'Types',
            ])
            ->contain('Monster1', function (SelectQuery $q) use ($user_id) {
                return $q
                    ->where(['Monster1.user_id' => $user_id]);
            })
            ->contain('Monster2', function (SelectQuery $q) use ($user_id) {
                return $q
                    ->where(['Monster2.user_id' => $user_id]);
            })
            ->contain('Monster3', function (SelectQuery $q) use ($user_id) {
                return $q
                    ->where(['Monster3.user_id' => $user_id]);
            })
            ->contain('Monster4', function (SelectQuery $q) use ($user_id) {
                return $q
                    ->where(['Monster4.user_id' => $user_id]);
            })
        ));
	}
	
	public function view($id = null) {
		$user_id = $this->user->id;
        $skill = $this->Skills
            ->find()
            ->where([
                'Skills.id' => $id,
            ])
            ->contain([
                'Types',
                'SkillEffects' => [
                    'SecondarySkillEffects'
                ],
            ])
			->contain('UserSkills', function (SelectQuery $q) use ($user_id) {
                return $q
                    ->where([
                        'UserSkills.user_id' => $user_id
                    ]);
            })
            ->firstOrFail();
		$this->set('skill', $skill);
        $status_effects = $this->fetchTable('Statuses')
            ->find()
            ->where([
				'Statuses.type !=' => 'Status'
			])
            ->all()
            ->toList();
        $status_effects_list = [];
        foreach($status_effects as $status_effect) {
            $status_effects_list[$status_effect->class] = $status_effect->name;
        }
		$statuses = [''=> 'N/A', 'random_buff' => 'Random Buff','all_buffs' => 'All Buffs', 'random_debuff' => 'Random Debuff', 'all_debuffs' => 'All Debuffs', 'random_buff_debuff' => 'Random Buff or Debuff', 'all_buffs_debuffs' => 'All Buffs and Debuffs'] + $status_effects_list;
		$this->set('status_options', $statuses);
	}

    public function transmute($id = null) {
		$user_id = $this->user->id;
        $skill = $this->Skills
            ->find()
            ->where([
                'Skills.id' => $id,
            ])
			->matching('UserSkills', function (SelectQuery $q) use ($user_id) {
                return $q
                    ->where([
                        'UserSkills.user_id' => $user_id
                    ]);
            })
			->contain('UserSkills', function (SelectQuery $q) use ($user_id) {
                return $q
                    ->where([
                        'UserSkills.user_id' => $user_id
                    ]);
            })
            ->firstOrFail();
        $cost = pow(5, $this->Skills->rarityLevels()[$skill->rarity] + 1);

		$types = $this->Skills->Types
            ->find()
            ->where([
				'Types.name != "Neutral"'
			])
            ->all()
            ->toList();
		if ($this->request->is(array('post', 'put'))) {

            if($this->user->rune_shards < $cost) {
                $this->Flash->error(__('You do not have '.$cost.' Rune Shards.'));
                return $this->redirect(['action' => 'transmute', $skill->id]);
            }
            $type_id = $this->request->getData()['type'];
            $possible_types = [];
            if($type_id > 0) {
                $cost = $cost * 2;
                $possible_types[] = $type_id;
            }
            $new_skill = $this->Skills->UserSkills->addRandomSkillToUser($this->user->id, $skill->rarity, $possible_types);
            if(!empty($new_skill->id)) {
                //remove access to old skill
                $this->Skills->UserSkills->delete($skill->user_skills[0]);
                //reduce rune shards by cost
                $this->user = $this->fetchTable('Users')->patchEntity($this->user, ['rune_shards' => $this->user->rune_shards - $cost], ['validate' => false]);
		        $this->fetchTable('Users')->save($this->user);
                $this->Flash->success(__($skill->name.' transmuted into '.$new_skill->name.'!'));
                return $this->redirect(['action' => 'view', $new_skill->id]);
            }else{
                $type_text = '';
                if($type_id > 0) {
                    foreach($types as $type) {
                        if($type->id == $type_id) {
                            $type_text = $type->name.' ';
                            break;
                        }
                    }
                }
                $this->Flash->error(__('Seems you already have all '.$type_text.$skill->rarity.' skills! We didn\'t go forward with the transmutation.'));
                return $this->redirect(['action' => 'my-skills']);
            }
        }
        $type_options[0] = 'Random ('.$cost.' Rune Shards)';
        foreach($types as $type) {
            $type_options[$type->id] = $type->name.' ('.($cost * 2).' Rune Shards)';
        }
		$this->set(compact('skill','type_options'));
        
	}
}