<?php
App::uses('AppController', 'Controller');
/**
 * Skills Controller
 *
 * @property Skill $Skill
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 * @property FlashComponent $Flash
 */
class SkillsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session', 'Flash');
	
	public $uses = [
		'Skill',
		'Status'
	];

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->Skill->recursive = 2;
		$this->Paginator->settings = [
			'Skill' => [
				'order' => [
					'Skill.type_id ASC',
					'Skill.rarity DESC'
				],
				'limit' => 200
			]
		];
		$this->set('skills', $this->Paginator->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		if (!$this->Skill->exists($id)) {
			throw new NotFoundException(__('Invalid skill'));
		}
		$options = array('conditions' => array('Skill.' . $this->Skill->primaryKey => $id), 'recursive' => 2);
		$this->set('skill', $this->Skill->find('first', $options));
		$statuses = [''=> 'N/A', 'random_buff' => 'Random Buff','all_buffs' => 'All Buffs', 'random_debuff' => 'Random Debuff', 'all_debuffs' => 'All Debuffs', 'random_buff_debuff' => 'Random Buff or Debuff', 'all_buffs_debuffs' => 'All Buffs and Debuffs'] + $this->Status->find('list', [
			'conditions' => [
				'Status.type !=' => 'Status'
			],
			'fields' => [
				'Status.class',
				'Status.name'
			]
		]);
		$this->set('status_options', $statuses);
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->Skill->create();
			if ($this->Skill->save($this->request->data)) {
				$this->Flash->success(__('The skill has been saved.'));
				return $this->redirect(array('action' => 'view', $this->Skill->id));
			} else {
				$this->Flash->error(__('The skill could not be saved. Please, try again.'));
			}
		}
		$types = $this->Skill->Type->find('list');
		$this->set(compact('types'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		if (!$this->Skill->exists($id)) {
			throw new NotFoundException(__('Invalid skill'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Skill->save($this->request->data)) {
				$this->Flash->success(__('The skill has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The skill could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Skill.' . $this->Skill->primaryKey => $id));
			$this->request->data = $this->Skill->find('first', $options);
		}
		$types = $this->Skill->Type->find('list');
		$this->set(compact('types'));
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		$this->Skill->id = $id;
		if (!$this->Skill->exists()) {
			throw new NotFoundException(__('Invalid skill'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Skill->delete()) {
			$this->Flash->success(__('The skill has been deleted.'));
		} else {
			$this->Flash->error(__('The skill could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
	
	public function view($id = null) {
		if (!$this->Skill->exists($id)) {
			throw new NotFoundException(__('Invalid skill'));
		}
		$options = array('conditions' => array('Skill.' . $this->Skill->primaryKey => $id), 'recursive' => 2);
		$this->set('skill', $this->Skill->find('first', $options));
		$statuses = [''=> 'N/A', 'random_buff' => 'Random Buff','all_buffs' => 'All Buffs', 'random_debuff' => 'Random Debuff', 'all_debuffs' => 'All Debuffs', 'random_buff_debuff' => 'Random Buff or Debuff', 'all_buffs_debuffs' => 'All Buffs and Debuffs'] + $this->Status->find('list', [
			'conditions' => [
				'Status.type !=' => 'Status'
			],
			'fields' => [
				'Status.class',
				'Status.name'
			]
		]);
		$this->set('status_options', $statuses);
	}
}
