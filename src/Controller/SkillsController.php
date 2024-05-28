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
class SkillsController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authentication->addUnauthenticatedActions([]);
    }


    public function mySkills() {
        $user_id = $this->user->id;
		$this->set('skills', $this->paginate($this->Skills
            ->find()
            ->matching('UserSkills', function ($q) use ($user_id) {
                return $q->where(['UserSkills.user_id' => $user_id]);
            })
            ->contain([
                'Types',
            ])
            ->contain('Monster1', function (SelectQuery $q) use ($user_id) {
                return $q
                    ->where(['Monster1.user_id' => $user_id]);
            })
            ->contain('Monster2', function (SelectQuery $q) use ($user_id) {
                return $q
                    ->where(['Monster2.user_id' => $user_id]);
            })
            ->contain('Monster3', function (SelectQuery $q) use ($user_id) {
                return $q
                    ->where(['Monster3.user_id' => $user_id]);
            })
            ->contain('Monster4', function (SelectQuery $q) use ($user_id) {
                return $q
                    ->where(['Monster4.user_id' => $user_id]);
            })
        ));
	}
	
	public function view($id = null) {
        $skill = $this->Skills
            ->find()
            ->where([
                'Skills.id' => $id,
            ])
            ->contain([
                'Types',
                'SkillEffects' => [
                    'SecondarySkillEffects'
                ]
            ])
            ->firstOrFail();
		$this->set('skill', $skill);
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