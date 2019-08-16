<?php
App::uses('AppController', 'Controller');
/**
 * Users Controller
 *
 * @property User $User
 * @property PaginatorComponent $Paginator
 */
class RunesController extends AppController {
	
	
	public $uses = [
		'Rune',
		'User'
	];
/**
 * admin_add method
 *
 * @return void
 */
	public function create() {
		$user_id = $this->Auth->user('id');
		$user = $this->User->findById($user_id);
		if($user['User']['rune_shards'] < 5) {
			$this->Flash->error(__('You do not have 5 Rune Shards. Battle in the Gauntlet to get some!'));
			return $this->redirect(['controller' => 'users', 'action' => 'my_runes']);
		}
		if ($this->request->is('post')) {
			$this->Rune->create();
			$this->request->data['Rune']['user_id'] = $user['User']['id'];
			$this->request->data['Rune'][$this->request->data['Rune']['first_upgrade']] = 1;
			if ($this->Rune->save($this->request->data)) {
				$this->User->id = $user['User']['id'];
				$this->User->saveField('rune_shards', $user['User']['rune_shards'] - 5);
				$this->Flash->success(__('The rune has been created.'));
				return $this->redirect(array('controller' => 'users', 'action' => 'my_runes'));
			} else {
				$this->Flash->error(__('The rune could not be saved. Please, try again.'));
			}
		}
		$types = $this->Rune->Type->find('list', [
			'conditions' => [
				'Type.name != "Neutral"'
			]
		]);
		$upgrade_options = $this->Rune->upgrades();
		$this->set(compact('types','upgrade_options'));
	}
	
	public function upgrade($id = null) {
		$user_id = $this->Auth->user('id');
		$user = $this->User->findById($user_id);
		$rune = $this->Rune->find('first', [
			'conditions' => [
				'Rune.id' => $id,
				'Rune.user_id' => $user_id
			]
		]);
		if (empty($rune['Rune']['id'])) {
			throw new NotFoundException(__('Invalid rune'));
		}
		
		if ($this->request->is(array('post', 'put'))) {
			//validate cost
			$cost = pow(5, $rune['Rune']['level'] + 1);
			if($rune['Rune'][$this->request->data['Rune']['upgrade']] > 0) {
				$cost += pow(5, $rune['Rune'][$this->request->data['Rune']['upgrade']]);
			}
			if($cost <= $user['User']['rune_shards']) {
				$this->request->data['Rune']['level'] = $rune['Rune']['level'] + 1;
				$this->request->data['Rune'][$this->request->data['Rune']['upgrade']] = $rune['Rune'][$this->request->data['Rune']['upgrade']] + 1;
				if ($this->Rune->save($this->request->data)) {
					$this->User->id = $user['User']['id'];
					$this->User->saveField('rune_shards', $user['User']['rune_shards'] - $cost);
					$this->Flash->success(__('The rune has been created.'));
					return $this->redirect(array('controller' => 'users', 'action' => 'my_runes'));
				} else {
					$this->Flash->error(__('The rune could not be saved. Please, try again.'));
				}
			}else{
				$this->Flash->error(__('You do not have enough Rune Shards for this Upgrade.'));
			}
		} else {
			$this->request->data = $this->Rune->find('first', [
				'conditions' => [
					'Rune.id' => $id
				]
			]);
		}
		$types = $this->Rune->Type->find('list');
		$upgrades = $this->Rune->upgrades();
		foreach($upgrades as $field=>$upgrade) {
			if($rune['Rune'][$field] < 5 && ($rune['Rune']['unlock_type'] == 0 || $field != 'unlock_type')) {
				$cost = pow(5, $rune['Rune']['level'] + 1);
				if($rune['Rune'][$field] > 0) {
					$cost += pow(5, $rune['Rune'][$field]);
				}
				$upgrade_options[$field] = 'Upgrade '.$upgrade.' for '.$cost.' Rune Shards';
			}
		}
		
		$this->set(compact('types','upgrade_options'));
		
	}
	
}