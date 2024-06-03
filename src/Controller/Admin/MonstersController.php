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
class MonstersController extends AppController
{

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authentication->addUnauthenticatedActions([]);
    }

    public function index() {
        $name = $this->request->getQuery('name');
        $where = [];
        if(!empty($name)) $where['Monsters.name LIKE'] = '%'.$name.'%';
		$this->paginate = [
			'Monsters' => [
				'order' => [
					'elo_rating' => 'DESC',
					'name' => 'ASC',
				],
			]
		];
		$monsters = $this->paginate($this->Monsters
            ->find()
			->where($where)
			->contain([
				'Users',
				'Types',
				'SecondaryTypes',
				'Skill1',
				'Skill2',
				'Skill3',
				'Skill4',
				'Ultimates'
			])
        );
        $this->set(compact(['monsters']));
	}
}