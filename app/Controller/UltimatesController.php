<?php
App::uses('AppController', 'Controller');
/**
 * Ultimates Controller
 *
 * @property Ultimate $Ultimate
 * @property PaginatorComponent $Paginator
 */
class UltimatesController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session', 'Flash');
	
/** Uses
 *
 * @var array
 */
	public $uses = [
		'Ultimate',
		'SkillEffect',
		'Status'
	];

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->Ultimate->recursive = 2;
		$this->Paginator->settings = [
			'Ultimate' => [
				'order' => [
					'Ultimate.type_id ASC',
					'Ultimate.rarity DESC'
				],
				'limit' => 200
			]
		];
		$this->set('ultimates', $this->Paginator->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		if (!$this->Ultimate->exists($id)) {
			throw new NotFoundException(__('Invalid ultimate'));
		}
		$options = array('conditions' => array('Ultimate.' . $this->Ultimate->primaryKey => $id), 'recursive' => 2);
		$this->set('ultimate', $this->Ultimate->find('first', $options));
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
			$this->Ultimate->create();
			if ($this->Ultimate->save($this->request->data)) {
				$this->Flash->success(__('The ultimate has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The ultimate could not be saved. Please, try again.'));
			}
		}
		$types = $this->Ultimate->Type->find('list');
		$secondaryTypes = $this->Ultimate->SecondaryType->find('list');
		$this->set(compact('types', 'secondaryTypes'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		if (!$this->Ultimate->exists($id)) {
			throw new NotFoundException(__('Invalid ultimate'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Ultimate->save($this->request->data)) {
				$this->Flash->success(__('The ultimate has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The ultimate could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Ultimate.' . $this->Ultimate->primaryKey => $id));
			$this->request->data = $this->Ultimate->find('first', $options);
		}
		$types = $this->Ultimate->Type->find('list');
		$secondaryTypes = $this->Ultimate->SecondaryType->find('list');
		$this->set(compact('types', 'secondaryTypes'));
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		$this->Ultimate->id = $id;
		if (!$this->Ultimate->exists()) {
			throw new NotFoundException(__('Invalid ultimate'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Ultimate->delete()) {
			$this->Flash->success(__('The ultimate has been deleted.'));
		} else {
			$this->Flash->error(__('The ultimate could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
/**
 * admin_add_skill_effect method
 *
 * @return void
 */
	public function admin_add_skill_effect($ultimate_id = null) {
		if (!$this->Ultimate->exists($ultimate_id)) {
			throw new NotFoundException(__('Invalid skill'));
		}
		$options = array('conditions' => array('Ultimate.' . $this->Ultimate->primaryKey => $ultimate_id));
		$this->set('ultimate', $this->Ultimate->find('first', $options));
		if ($this->request->is('post')) {
			$this->SkillEffect->create();
			$this->request->data['SkillEffect']['ultimate_id'] = $ultimate_id;
			if ($this->SkillEffect->save($this->request->data)) {
				$this->Flash->success(__('The skill effect has been saved.'));
				return $this->redirect(array('controller' => 'ultimates', 'action' => 'view', $ultimate_id));
			} else {
				$this->Flash->error(__('The skill effect could not be saved. Please, try again.'));
			}
		}
		$skillEffects = [0 => 'Nothing'] + $this->SkillEffect->find('list', [
			'conditions' => [
				'SkillEffect.ultimate_id' => $ultimate_id,
				'SkillEffect.skill_effect_id' => 0
			]
		]);
		$statuses = [''=> 'N/A', 'random_buff' => 'Random Buff','all_buffs' => 'All Buffs', 'random_debuff' => 'Random Debuff', 'all_debuffs' => 'All Debuffs', 'random_buff_debuff' => 'Random Buff or Debuff', 'all_buffs_debuffs' => 'All Buffs and Debuffs'] + $this->Status->find('list', [
			'conditions' => [
				'Status.type !=' => 'Status'
			],
			'fields' => [
				'Status.class',
				'Status.name'
			]
		]);
		$this->set(compact('skillEffects','statuses'));
	}

/**
 * admin_edit_skill_effect method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit_skill_effect($id = null) {
		if (!$this->SkillEffect->exists($id)) {
			throw new NotFoundException(__('Invalid skill effect'));
		}
		$options = array('conditions' => array('SkillEffect.' . $this->SkillEffect->primaryKey => $id));
		$skill_effect = $this->SkillEffect->find('first', $options);
		$options = array('conditions' => array('Ultimate.' . $this->Ultimate->primaryKey => $skill_effect['SkillEffect']['ultimate_id']));
		$ultimate = $this->Ultimate->find('first', $options);
		if ($this->request->is(array('post', 'put'))) {
			if ($this->SkillEffect->save($this->request->data)) {
				$this->Flash->success(__('The skill effect has been saved.'));
				return $this->redirect(array('controller' => 'ultimates', 'action' => 'view', $ultimate['Ultimate']['id']));
			} else {
				$this->Flash->error(__('The skill effect could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('SkillEffect.' . $this->SkillEffect->primaryKey => $id));
			$this->request->data = $this->SkillEffect->find('first', $options);
		}
		$skillEffects = [0 => 'Nothing'] + $this->SkillEffect->find('list', [
			'conditions' => [
				'SkillEffect.ultimate_id' => $this->request->data['SkillEffect']['ultimate_id'],
				'SkillEffect.skill_effect_id' => 0
			]
		]);
		$statuses = [''=> 'N/A', 'random_buff' => 'Random Buff','all_buffs' => 'All Buffs', 'random_debuff' => 'Random Debuff', 'all_debuffs' => 'All Debuffs', 'random_buff_debuff' => 'Random Buff or Debuff', 'all_buffs_debuffs' => 'All Buffs and Debuffs'] + $this->Status->find('list', [
			'conditions' => [
				'Status.type !=' => 'Status'
			],
			'fields' => [
				'Status.class',
				'Status.name'
			]
		]);
		$this->set(compact('skillEffects','statuses'));
	}

/**
 * admin_delete_skill_effect method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete_skill_effect($ultimate_id, $id = null) {
		$this->SkillEffect->id = $id;
		if (!$this->SkillEffect->exists()) {
			throw new NotFoundException(__('Invalid effect'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->SkillEffect->delete()) {
			$this->Flash->success(__('The effect has been deleted.'));
		} else {
			$this->Flash->error(__('The effect could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'view', $ultimate_id));
	}
	
	public function view($id = null) {
		if (!$this->Ultimate->exists($id)) {
			throw new NotFoundException(__('Invalid ultimate'));
		}
		$options = array('conditions' => array('Ultimate.' . $this->Ultimate->primaryKey => $id),'recursive' => 2);
		$this->set('ultimate', $this->Ultimate->find('first', $options));
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