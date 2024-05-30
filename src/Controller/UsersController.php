<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
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
class UsersController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authentication->addUnauthenticatedActions(['login','register']);
    }

    /**
     * login page
     *
     * @return \Cake\Http\Response|null
     */
    

    public function login() {
        $this->request->allowMethod(['get', 'post']);
        $result = $this->Authentication->getResult();
        if ($result->isValid()) {
            $redirect = $this->request->getQuery('redirect', [
            'controller' => 'Monsters',
            'action' => 'my-monsters',
            ]);
            return $this->redirect($redirect);
        }
        if ($this->request->is('post') && !$result->isValid()) {
            $this->Flash->error(__('Invalid name or password'));
        }
    }

    public function logout() {
        $result = $this->Authentication->getResult();
        if ($result->isValid()) {
            $this->Authentication->logout();
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }
    }

    public function register()
    {
        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $types = $this->fetchTable('Types')
                ->find()
                ->all();
            $type_counts = [];
            foreach($types as $type) {
                $type_counts[$type->name] = 0;
            }
            foreach($user->getRegistrationAnswerValues()[$data['favorite_dessert']] as $type) {
                $type_counts[$type]++;
            }
            foreach($user->getRegistrationAnswerValues()[$data['favorite_power_ranger']] as $type) {
                $type_counts[$type]++;
            }
            foreach($user->getRegistrationAnswerValues()[$data['favorite_nineties_cartoon']] as $type) {
                $type_counts[$type]++;
            }
            foreach($user->getRegistrationAnswerValues()[$data['favorite_pizza_topping']] as $type) {
                $type_counts[$type]++;
            }
            foreach($user->getRegistrationAnswerValues()[$data['favorite_day_of_the_week']] as $type) {
                $type_counts[$type]++;
            }
            foreach($user->getRegistrationAnswerValues()[$data['favorite_rpg_class']] as $type) {
                $type_counts[$type]++;
            }
            arsort($type_counts);
            $user = $this->Users->patchEntity($user, $data);

            if ($this->Users->save($user)) {
				$this->Authentication->setIdentity($user);
					
                $type_name = array_keys($type_counts)[0];
                $type_2_name = array_keys($type_counts)[1];
                $type_id = 1;
                $type_2_id = 2;
                foreach($types as $type) {
                    if($type->name == $type_name) {
                        $type_id = $type->id;
                    }
                    if($type->name == $type_2_name) {
                        $type_2_id = $type->id;
                    }
                }

                //create monster 
                $this->fetchTable('Monsters')->createMonsterForUser($user->id, $type_id);
                //create second monster 
                $this->fetchTable('Monsters')->createMonsterForUser($user->id, $type_2_id);

                //grant base skills
                $base_skills = [
                    2,
                    56,
                    57,
                    58
                ];
                foreach($base_skills as $base_skill_id) {
                    $this->fetchTable('UserSkills')->addSkillToUser($user->id, $base_skill_id);
                }
                $this->fetchTable('UserSkills')->addRandomSkillToUser($user->id, 'Uncommon', [$type_id]);
                $this->fetchTable('UserSkills')->addRandomSkillToUser($user->id, 'Uncommon', [$type_id]);
                $this->fetchTable('UserSkills')->addRandomSkillToUser($user->id, 'Uncommon', [$type_2_id]);
                $this->fetchTable('UserSkills')->addRandomSkillToUser($user->id, 'Uncommon', [$type_2_id]);
                //hyper beam
                $this->fetchTable('UserUltimates')->addUltimateToUser($user->id, 1);
                //holy nova
                $this->fetchTable('UserUltimates')->addUltimateToUser($user->id, 16);
                $this->Flash->success(__('Your have created your account!'));

                return $this->redirect(['controller'=>'monsters','action' => 'my-monsters']);
            }
            $this->Flash->error(__('Unable to register.'));
        }
        $this->set(compact('user'));
    }

    public function enterDreamMode() {
		if(!empty($this->user->dreaming_since)) {
			$this->Flash->error(__('You are already in dream mode.'));
			return $this->redirect(['controller' => 'monsters', 'action' => 'my-monsters']);
		}
		$this->user->dreaming_since = new DateTime();
        $this->Users->save($this->user);
		$this->Flash->success(__('Your monsters are now dreaming!'));
		return $this->redirect(['controller' => 'monsters','action' => 'my-monsters']);
    }


    public function exitDreamMode() {
		if(empty($this->user->dreaming_since)) {
			$this->Flash->error(__('Your monsters weren\'t dreaming.'));
			return $this->redirect(['controller' => 'monsters', 'action' => 'my-monsters']);
		}
        $this->user->dreaming_since = null;
		$this->user->gold += $this->user->dreamt_gold;
		$this->user->rune_shards += $this->user->dreamt_rune_shards;
        $this->Users->save($this->user);
        if($this->user->dreamt_gold > 0) {
		    $this->Flash->success(__('Your monsters came back from their dreams with '.$this->user->dreamt_gold.' gold'.($this->user->dreamt_rune_shards > 0 ? ' and '.$this->user->dreamt_rune_shards.' rune shard'.($this->user->dreamt_rune_shards == 1 ? '' : 's') : '' ).'!'));
        }else{
		    $this->Flash->success(__('Your monsters stopped dreaming.'));
        }
        return $this->redirect(['controller' => 'monsters','action' => 'my-monsters']);
    }
	
	public function purchaseRandomSingleTypeMonster() {
		if($this->user->gold < SINGLE_TYPE_MONSTER_COST) {
			$this->Flash->error(__('You do not have '.SINGLE_TYPE_MONSTER_COST.' Gold. Battle in the Gauntlet to get some!'));
			return $this->redirect(['controller' => 'monsters','action' => 'my-monsters']);
		}
		$this->user->gold -= SINGLE_TYPE_MONSTER_COST;
        $this->Users->save($this->user);
		
		$types = $this->fetchTable('Types')
            ->find()
            ->where([
				'Types.name != "Neutral"',
				'Types.name != "Flying"',
            ])
            ->all()
            ->toList();
		
		//check if they already have one of every type
        $user_id = $this->user->id;
		$doesnt_have_types = $this->fetchTable('Types')
            ->find()
            ->where([
				'Types.name != "Neutral"',
				'Types.name != "Flying"'
            ])
            ->notMatching('Monsters', function ($q) use ($user_id) {
                return $q->where(['Monsters.user_id' => $user_id]);
            })
            ->all()
            ->toList();
		
		$monster_type = null;
		
		while($monster_type == null) {
			if(!empty($doesnt_have_types)) {
				$monster_type = $doesnt_have_types[rand(0,count($doesnt_have_types) - 1)];
			}else{
				$monster_type = $types[rand(0,count($types) - 1)];
			}
		}
		$this->Users->Monsters->createMonsterForUser($user_id, $monster_type->id);
		$this->Flash->success(__('You gained a new '.$monster_type->name.' Type Monster!'));
		return $this->redirect(['controller' => 'monsters', 'action' => 'my-monsters']);
	}
	
	public function purchaseRandomDualTypeMonster() {
		if($this->user->gold < DUAL_TYPE_MONSTER_COST) {
			$this->Flash->error(__('You do not have '.DUAL_TYPE_MONSTER_COST.' Gold. Battle in the Gauntlet to get some!'));
			return $this->redirect(['controller' => 'monsters','action' => 'my-monsters']);
		}
		$this->user->gold -= DUAL_TYPE_MONSTER_COST;
        $this->Users->save($this->user);
		
		
		$types = $this->fetchTable('Types')
            ->find()
            ->where([
				'Types.name != "Neutral"',
				'Types.name != "Flying"',
            ])
            ->all()
            ->toList();
		
		//check if they have all combos
		$dual_monster_count = $this->Users->Monsters
            ->find()
            ->where([
				'Monsters.user_id' => $this->user->id,
				'Monsters.type_id != 0',
				'Monsters.secondary_type_id != 0'
			])
            ->contain([])
            ->count();
		$type_count = count($types);
		$has_all_combos = false;

		$combos = 1;
		if($type_count > 2) {
			$has_all_combos = false;
			$fact = $type_count;
			$ffact1 = 1;

			while($fact >= 1)
			{
				$ffact1 = $fact * $ffact1;
				$fact--;
			}
			$fact = $type_count - 2;
			$ffact2 = 1;
			while($fact >= 1)
			{
				$ffact2 = $fact * $ffact2;
				$fact--;
			}
			$combos = $ffact1 / (2 * $ffact2);
		}
        if($dual_monster_count >= $combos) {
			$has_all_combos = true;
		}
		
		$monster_type = null;
		$secondary_monster_type = null;
		while($monster_type == null) {
			$monster_type = $types[rand(0,count($types) - 1)];
			$secondary_monster_type = null;
			while($secondary_monster_type == null) {
				$secondary_monster_type = $types[rand(0,count($types) - 1)];
				if($monster_type->id == $secondary_monster_type->id) {
					$secondary_monster_type = null;
				}
			}
			if($has_all_combos == false) {
				//check if they have this monster
                $monster_check = $this->Users->Monsters
                    ->find()
                    ->where([
                        'Monsters.user_id' => $this->user->id,
                        'OR' => [
                            0 => [
                                'Monsters.type_id' => $monster_type->id,
                                'Monsters.secondary_type_id' => $secondary_monster_type->id
                            ],
                            1 => [
                                'Monsters.type_id' => $secondary_monster_type->id,
                                'Monsters.secondary_type_id' => $monster_type->id,
                            ]
                        ]
                    ])
                    ->contain([])
                    ->count();
				if($monster_check > 0) {
					$monster_type = null;
					$secondary_monster_type = null;
				}
			}
		}
		
		$this->Users->Monsters->createMonsterForUser($this->user->id, $monster_type->id, $secondary_monster_type->id);
		$this->Flash->success(__('You gained a new '.$monster_type->name.' and '.$secondary_monster_type->name.' Dual Type Monster!'));
		return $this->redirect(['controller' => 'monsters', 'action' => 'my-monsters']);
	}
	
	public function increaseActiveMonsterLimit() {
		$cost = 50 * $this->user->active_monster_limit;
		if($this->user->gems < $cost) {
			$this->Flash->error(__('You do not have '.$cost.' Gems.'));
			return $this->redirect(['controller' => 'monsters','action' => 'my-monsters']);
		}
		$this->user->gems -= $cost;
		$this->user->active_monster_limit += 1;
        $this->Users->save($this->user);
		$this->Flash->success(__('You can now have '.$this->user->active_monster_limit.' Monsters active in the Gauntlet at a time!'));
		return $this->redirect(['controller' => 'monsters','action' => 'my-monsters']);
	}
}