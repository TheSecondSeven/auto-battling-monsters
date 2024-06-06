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
class UltimatesController extends AppController
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
        $where['Ultimates.rarity !='] = 'Admin Only';
        if(!empty($rarity)) $where['Ultimates.rarity'] = $rarity;
        if(!empty($type_id)) {
            $where['OR'] = [];
            $where['OR']['Ultimates.type_id'] = $type_id;
            $where['OR']['Ultimates.secondary_type_id'] = $type_id;
        }
        if(!empty($name)) $where['Skills.name LIKE'] = '%'.$name.'%';

		$this->paginate = [
			'Ultimates' => [
				'order' => [
					'rarity' => 'DESC',
					'type_id' => 'ASC',
					'secondary_type_id' => 'ASC',
					'value' => 'DESC'
				],
			]
		];
		$ultimates = $this->paginate($this->Ultimates
            ->find()
            ->where($where)
			->contain([
				'Types',
                'SecondaryTypes'
			])
        );
        $rarities = $this->fetchTable('Skills')->rarities();

		$types = $this->Ultimates->Types->find('list')
            ->where([])
            ->all();
        $this->set(compact(['ultimates','rarities','types']));
	}
	
	public function myUltimates() {
        $user_id = $this->user->id;

        $this->paginate = [
            'sortableFields' => [
                'UserUltimates.new', 'rarity', 'name', 'type_id', 'cast_time', 'down_time'
            ],
            'order' => [
                'UserUltimates.new' => 'DESC',
                'rarity' => 'DESC',
                'Types.name' => 'ASC',
                'name' => 'ASC',
            ]
		];
		$this->set('ultimates', $this->paginate($this->Ultimates
            ->find()
            ->matching('UserUltimates', function ($q) use ($user_id) {
                return $q->where(['UserUltimates.user_id' => $user_id]);
            })
            ->contain([
                'Types',
                'SecondaryTypes'
            ])
            ->contain('Monsters', function (SelectQuery $q) use ($user_id) {
                return $q
                    ->where(['Monsters.user_id' => $user_id]);
            })
		));
	}
	
	public function view($id = null) {
		$user_id = $this->user->id;
        $ultimate = $this->Ultimates
            ->find()
            ->where([
                'Ultimates.id' => $id,
            ])
            ->contain([
                'Types',
                'SecondaryTypes',
                'SkillEffects' => [
                    'SecondarySkillEffects'
                ]
            ])
			->contain('UserUltimates', function (SelectQuery $q) use ($user_id) {
                return $q
                    ->where([
                        'UserUltimates.user_id' => $user_id
                    ]);
            })
            ->firstOrFail();
        $ultimate->user_ultimates[0]->new = 0;
        $this->Ultimates->UserUltimates->save($ultimate->user_ultimates[0]);
		$this->set('ultimate', $ultimate);
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
}