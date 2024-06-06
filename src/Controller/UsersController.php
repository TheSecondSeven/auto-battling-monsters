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
                $new_monster = $this->fetchTable('Monsters')->createMonsterForUser($user->id, $type_id);
                //create second monster 
                // $this->fetchTable('Monsters')->createMonsterForUser($user->id, $type_2_id);

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
                // $this->fetchTable('UserSkills')->addRandomSkillToUser($user->id, 'Uncommon', [$type_id]);
                // $this->fetchTable('UserSkills')->addRandomSkillToUser($user->id, 'Uncommon', [$type_id]);
                // $this->fetchTable('UserSkills')->addRandomSkillToUser($user->id, 'Uncommon', [$type_2_id]);
                // $this->fetchTable('UserSkills')->addRandomSkillToUser($user->id, 'Uncommon', [$type_2_id]);
                //hyper beam
                $this->fetchTable('UserUltimates')->addUltimateToUser($user->id, 1);
                //holy nova
                // $this->fetchTable('UserUltimates')->addUltimateToUser($user->id, 16);
                $this->Flash->success(__('Your have received your first monster! It\'s a '.$type_name.' Type. Give it a name!'));

                return $this->redirect(['controller'=>'monsters','action' => 'edit', $new_monster->id]);
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
        $dreamt_gold = 0;
        if($this->user->dreamt_gold > 0) {
            $dreamt_gold = $this->user->dreamt_gold;
		    $this->user->gold += $this->user->dreamt_gold;
            $this->user->dreamt_gold = 0;
        }
        $dreamt_rune_shards = 0;
        if($this->user->dreamt_rune_shards > 0) {
            $dreamt_rune_shards = $this->user->dreamt_rune_shards;
		    $this->user->rune_shards += $this->user->dreamt_rune_shards;
            $this->user->dreamt_rune_shards = 0;
        }
        $dreamt_gems = 0;
        if($this->user->dreamt_gems > 0) {
            $dreamt_gems = $this->user->dreamt_gems;
		    $this->user->gems += $this->user->dreamt_gems;
            $this->user->dreamt_gems = 0;
        }
        if($dreamt_gold > 0 || $dreamt_rune_shards > 0 || $dreamt_gems > 0) {
            $this->Users->save($this->user);
		    $this->Flash->success(__('Your monsters came back from their dreams with'.($dreamt_gold > 0 ? ' '.$dreamt_gold.' gold' : '').($dreamt_rune_shards > 0 ? ($dreamt_gold > 0 ? ' and' : '').' '.$dreamt_rune_shards.' rune shard'.($dreamt_rune_shards == 1 ? '' : 's') : '' ).($dreamt_gems > 0 ? ' and '.$dreamt_gems.' rune shard'.($dreamt_gems == 1 ? '' : 's') : '' ).'!'));
        }else{
            $this->Users->save($this->user);
		    $this->Flash->success(__('Your monsters stopped dreaming.'));
        }
        return $this->redirect(['controller' => 'monsters','action' => 'my-monsters']);
    }
	
	public function purchaseRandomSingleTypeMonster() {
		if($this->user->gold < SINGLE_TYPE_MONSTER_COST) {
			$this->Flash->error(__('You do not have '.SINGLE_TYPE_MONSTER_COST.' Gold. Battle in the Gauntlet to get some!'));
			return $this->redirect(['controller' => 'monsters','action' => 'my-monsters']);
		}
		$monster = $this->Users->Monsters->createMonsterForUser($this->user->id);
        if(!empty($monster->id)) {
            $this->Users->removeGoldFromUser($this->user->id, SINGLE_TYPE_MONSTER_COST);
            $type = $this->fetchTable('Types')
                ->find()
                ->where([
                    'Types.id' => $monster->type_id
                ])
                ->first();
            $this->Flash->success(__('You gained a new '.$type->name.' Type Monster!'));
		    return $this->redirect(['controller' => 'monsters', 'action' => 'edit', $monster->id]);
        }else{
            $this->Flash->error(__('Error creating monster. Please try again.'));
		    return $this->redirect(['controller' => 'monsters', 'action' => 'my-monsters']);
        }
	}
	
	public function purchaseRandomDualTypeMonster() {
		if($this->user->gold < DUAL_TYPE_MONSTER_COST) {
			$this->Flash->error(__('You do not have '.DUAL_TYPE_MONSTER_COST.' Gold. Battle in the Gauntlet to get some!'));
			return $this->redirect(['controller' => 'monsters','action' => 'my-monsters']);
		}

		$monster = $this->Users->Monsters->createDualTypeMonsterForUser($this->user->id);
        if(!empty($monster->id)) {
            $this->Users->removeGoldFromUser($this->user->id, DUAL_TYPE_MONSTER_COST);
            $type = $this->fetchTable('Types')
                ->find()
                ->where([
                    'Types.id' => $monster->type_id
                ])
                ->first();
            $secondary_type = $this->fetchTable('Types')
                ->find()
                ->where([
                    'Types.id' => $monster->secondary_type_id
                ])
                ->first();
            $this->Flash->success('You gained a new '.$type->name.' and '.$secondary_type->name.' Dual Type Monster!');
		    return $this->redirect(['controller' => 'monsters', 'action' => 'edit', $monster->id]);
        }else{
            $this->Flash->error(__('Error creating monster. Please try again.'));
		    return $this->redirect(['controller' => 'monsters', 'action' => 'my-monsters']);
        }
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