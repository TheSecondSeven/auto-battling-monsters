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
class StatusesController extends AppController
{
	private $aoe_value = 1.5;
	private $healing_value = 0.9;

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authentication->addUnauthenticatedActions([]);
    }

    public function index() {
        $type = $this->request->getQuery('type');
        $name = $this->request->getQuery('name');
        $where = [];
        if(!empty($type)) $where['Statuses.type'] = $type;
        if(!empty($name)) $where['Statuses.name LIKE'] = '%'.$name.'%';
		$this->paginate = [
			'Statuses' => [
				'order' => [
					'name' => 'ASC'
				],
			]
		];
		$statuses = $this->paginate($this->Statuses
            ->find()
			->where($where)
        );

        $this->set(compact(['statuses']));
	}

	public function view($id = null) {
		$this->set('status', $this->Statuses
            ->find()
            ->where([
                'Statuses.id' => $id
            ])
			->first()
        );
	}
    
	public function create() {
        $status = $this->Statuses->newEmptyEntity();
		if ($this->request->is('post')) {
            $status = $this->Statuses->patchEntity($status, $this->request->getData());
			if ($this->Statuses->save($status)) {
				$this->Flash->success(__('The status has been created.'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('The status could not be saved. Please, try again.'));
			}
		}
        $effects = ['' => 'None'] + $this->fetchTable('SkillEffects')->effects();
        $types = ['Status'=>'Status','Buff'=>'Buff','Debuff'=>'Debuff'];
		$this->set(compact('status','types','effects'));
	}
    
	public function update($id = null) {
		$status = $this->Statuses
            ->find()
            ->where([
                'Statuses.id' => $id
            ])
            ->firstOrFail();
		if ($this->request->is(array('post', 'put'))) {
            $status = $this->Statuses->patchEntity($status, $this->request->getData());
			if ($this->Statuses->save($status)) {
				$this->Flash->success(__('The status has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The status could not be saved. Please, try again.'));
			}
		}
        $effects = ['' => 'None'] + $this->fetchTable('SkillEffects')->effects();
        $types = ['Status'=>'Status','Buff'=>'Buff','Debuff'=>'Debuff'];
		$this->set(compact('status','types','effects'));
	}

	public function delete($id = null) {
		$status = $this->Statuses
            ->find()
            ->where([
                'Statuses.id' => $id
            ])
            ->firstOrFail();
		if ($this->Statuses->delete($status)) {
			$this->Flash->success(__('The status has been deleted.'));
		} else {
			$this->Flash->error(__('The status could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}