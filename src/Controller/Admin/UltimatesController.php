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
class UltimatesController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authentication->addUnauthenticatedActions([]);
    }
	
	public function index() {
		$this->set('ultimates', $this->paginate($this->Ultimates
            ->find()
            ->contain([
                'Types',
                'SecondaryTypes'
            ])
            ->contain('Monsters', function (SelectQuery $q) {
                return $q
                    ->select(['Monsters.id','Monsters.ultimate_id',  'Monsters.name']);
            })
		));
	}
	
	public function view($id = null) {
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
            ->firstOrFail();
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