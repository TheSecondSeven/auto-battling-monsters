<?php
declare(strict_types=1);
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\View\Exception\MissingTemplateException;
use Cake\Event\EventInterface;

/**
 * Static content controller
 *
 * This controller will render views from templates/Pages/
 *
 * @link https://book.cakephp.org/4/en/controllers/pages-controller.html
 */
class StatusesController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authentication->addUnauthenticatedActions([]);
    }

    public function index() {
		$statuses = $this->Statuses
            ->find()
			->order([
                'Statuses.name ASC'
            ])
            ->all();
        $this->set(compact(['statuses']));
	}

    public function view($id) {
		$status = $this->Statuses
            ->find()
            ->where([
                'Statuses.id' => $id
            ])
			->firstOrFail();
        $status->skills = $this->fetchTable('Skills')
                ->find() 
                ->matching('SkillEffects', function ($q) use ($status) {
                    return $q->where([
                        'OR' => [
                            'SkillEffects.status' => $status->class,
                            'SkillEffects.effect' => $status->effect
                        ]
                    ]);
                })
                ->contain([
                    'Types'
                ])
                ->all()
                ->toList() +
            $this->fetchTable('Skills')
                ->find() 
                ->matching('SkillEffects', function ($q) use ($status) {
                    return $q->matching('SecondarySkillEffects', function ($qu) use ($status) {
                        return $qu->where([
                            'OR' => [
                                'SecondarySkillEffects.status' => $status->class,
                                'SecondarySkillEffects.effect' => $status->effect
                            ]
                        ]);
                    });
                })
                ->contain([
                    'Types'
                ])
                ->all()
                ->toList();

        $this->set(compact(['status']));
	}

}