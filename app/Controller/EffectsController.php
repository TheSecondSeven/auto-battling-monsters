<?php
App::uses('AppController', 'Controller');
/**
 * Effects Controller
 *
 * @property Effect $Effect
 * @property PaginatorComponent $Paginator
 */
class EffectsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Effect->recursive = 0;
		$this->set('effects', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Effect->exists($id)) {
			throw new NotFoundException(__('Invalid effect'));
		}
		$options = array('conditions' => array('Effect.' . $this->Effect->primaryKey => $id));
		$this->set('effect', $this->Effect->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Effect->create();
			if ($this->Effect->save($this->request->data)) {
				$this->Flash->success(__('The effect has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The effect could not be saved. Please, try again.'));
			}
		}
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Effect->exists($id)) {
			throw new NotFoundException(__('Invalid effect'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Effect->save($this->request->data)) {
				$this->Flash->success(__('The effect has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The effect could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Effect.' . $this->Effect->primaryKey => $id));
			$this->request->data = $this->Effect->find('first', $options);
		}
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Effect->id = $id;
		if (!$this->Effect->exists()) {
			throw new NotFoundException(__('Invalid effect'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Effect->delete()) {
			$this->Flash->success(__('The effect has been deleted.'));
		} else {
			$this->Flash->error(__('The effect could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->Effect->recursive = 0;
		$this->set('effects', $this->Paginator->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		if (!$this->Effect->exists($id)) {
			throw new NotFoundException(__('Invalid effect'));
		}
		$options = array('conditions' => array('Effect.' . $this->Effect->primaryKey => $id));
		$this->set('effect', $this->Effect->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->Effect->create();
			if ($this->Effect->save($this->request->data)) {
				$this->Flash->success(__('The effect has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The effect could not be saved. Please, try again.'));
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
		if (!$this->Effect->exists($id)) {
			throw new NotFoundException(__('Invalid effect'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Effect->save($this->request->data)) {
				$this->Flash->success(__('The effect has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The effect could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Effect.' . $this->Effect->primaryKey => $id));
			$this->request->data = $this->Effect->find('first', $options);
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
		$this->Effect->id = $id;
		if (!$this->Effect->exists()) {
			throw new NotFoundException(__('Invalid effect'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Effect->delete()) {
			$this->Flash->success(__('The effect has been deleted.'));
		} else {
			$this->Flash->error(__('The effect could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
