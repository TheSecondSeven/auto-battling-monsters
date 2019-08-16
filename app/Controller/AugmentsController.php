<?php
App::uses('AppController', 'Controller');
/**
 * Augments Controller
 *
 * @property Augment $Augment
 * @property PaginatorComponent $Paginator
 */
class AugmentsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');
	public $uses = [
		'Augment',
		'Type'
	];
	
	public $common_skills = [
		[
			'field' => 'skill_1',
			'prefix' => 'Primary',
			'description' => 'First'
		],
		[
			'field' => 'skill_2',
			'prefix' => 'Secondary',
			'description' => 'Second'
		],
		[
			'field' => 'skill_3',
			'prefix' => 'Tertiary',
			'description' => 'Third'
		],
		[
			'field' => 'skill_4',
			'prefix' => 'Quaternary',
			'description' => 'Fourth'
		]
	];
	public function populate_uncommon_augments() {
		$types = $this->Type->find('all');
		foreach($types as $type) {
			if($type['Type']['name'] != 'Neutral') {
				$this->Augment->create();
				$augment['Augment'] = [
					'id' => null,
					'rarity' => 'Rare',
					'name' => $type['Type']['name'].' Unlock Rune',
					'description' => 'Allows '.$type['Type']['name'].' Skills to be used in the Ultimate Slot.',
					'ultimate' => 1,
					'type' => 'Type',
					'amount_1' => $type['Type']['id']
				];
				$this->Augment->save($augment);
			}
		}
		exit;
	}

	public function populate_common_augments() {
		/*
		$types = $this->Type->find('all');
		foreach($types as $type) {
			$this->populateCommonType($type);
			$this->populateCommonDamage($type);
			$this->populateCommonHealing($type);
			$this->populateCommonCastAgain($type);
			
		}
		*/
		exit;
		
	}
	public function populateCommonType($type) {
		foreach($this->common_skills as $skill) {
			if($type['Type']['name'] != 'Neutral') {
				$this->Augment->create();
				$augment['Augment'] = [
					'id' => null,
					'rarity' => 'Common',
					'name' => $type['Type']['name'].' Unlock Rune',
					'description' => 'Allows '.$type['Type']['name'].' Skills to be used in this Slot.',
					$skill['field'] => 1,
					'type' => 'Type',
					'amount_1' => $type['Type']['id']
				];
				$this->Augment->save($augment);
			}
		}
	}
	public function populateCommonDamage($type) {
		foreach($this->common_skills as $skill) {
			$this->Augment->create();
			$augment['Augment'] = [
				'id' => null,
				'rarity' => 'Common',
				'name' => $type['Type']['name'].' Damage Rune',
				'description' => 'Increases the damage of '.$type['Type']['name'].' Skills used in the this Slot by 5%.',
				$skill['field'] => 1,
				'type' => 'Damage',
				'amount_1' => $type['Type']['id'],
				'amount_2' => 5
			];
			$this->Augment->save($augment);
		}
	}
	public function populateCommonHealing($type) {
		foreach($this->common_skills as $skill) {
			$this->Augment->create();
			$augment['Augment'] = [
				'id' => null,
				'rarity' => 'Common',
				'name' => $type['Type']['name'].' Healing Rune',
				'description' => 'Increases the healing of '.$type['Type']['name'].' Skills used in the this Slot by 5%.',
				$skill['field'] => 1,
				'type' => 'Healing',
				'amount_1' => $type['Type']['id'],
				'amount_2' => 5
			];
			$this->Augment->save($augment);
		}
	}
	public function populateCommonCastAgain($type) {
		foreach($this->common_skills as $skill) {
			$this->Augment->create();
			$augment['Augment'] = [
				'id' => null,
				'rarity' => 'Common',
				'name' => $type['Type']['name'].' Overload Rune',
				'description' => '10% Chance of casting '.$type['Type']['name'].' Skills used in the this Slot a second time.',
				$skill['field'] => 1,
				'type' => 'Chance To Cast Again',
				'amount_1' => $type['Type']['id'],
				'amount_2' => 10
			];
			$this->Augment->save($augment);
		}
	}


/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->Augment->recursive = 0;
		$this->set('augments', $this->Paginator->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		if (!$this->Augment->exists($id)) {
			throw new NotFoundException(__('Invalid augment'));
		}
		$options = array('conditions' => array('Augment.' . $this->Augment->primaryKey => $id));
		$this->set('augment', $this->Augment->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->Augment->create();
			if ($this->Augment->save($this->request->data)) {
				$this->Flash->success(__('The augment has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The augment could not be saved. Please, try again.'));
			}
		}
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		if (!$this->Augment->exists($id)) {
			throw new NotFoundException(__('Invalid augment'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Augment->save($this->request->data)) {
				$this->Flash->success(__('The augment has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The augment could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Augment.' . $this->Augment->primaryKey => $id));
			$this->request->data = $this->Augment->find('first', $options);
		}
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		$this->Augment->id = $id;
		if (!$this->Augment->exists()) {
			throw new NotFoundException(__('Invalid augment'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Augment->delete()) {
			$this->Flash->success(__('The augment has been deleted.'));
		} else {
			$this->Flash->error(__('The augment could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
