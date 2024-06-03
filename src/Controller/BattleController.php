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
class BattleController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authentication->addUnauthenticatedActions([]);

        $this->loadComponent('Combat');
    }

    public function practice($monster_id) {
		$monster = $this->fetchTable('Monsters')
            ->find('forBattle')
            ->where([
                'Monsters.id' => $monster_id,
                'Monsters.user_id' => $this->user->id
            ])
            ->firstOrFail();
		$opponent = null;
		$elo_threshold = 1;
		while(empty($opponent->id)) {
			$where = [
				'NOT' => [
					'Monsters.id' => $monster->id
				],
				'Monsters.skill_1_id != 0',
				'Monsters.skill_2_id != 0',
				'Monsters.skill_3_id != 0',
				'Monsters.skill_4_id != 0',
				'Monsters.ultimate_id != 0'
			];
			if($elo_threshold < 10) {
				$elo_threshold_amount = $elo_threshold * 200;
				$where['Monsters.elo_rating >='] = $monster->elo_rating - $elo_threshold_amount;
				$where['Monsters.elo_rating <='] = $monster->elo_rating + $elo_threshold_amount;
			}
			$opponent = $this->fetchTable('Monsters')
                ->find('forBattle')
                ->where($where)
                ->order([
                    'RAND()'
                ])
				->first();
			$elo_threshold++;
		}
		$result = $this->Combat->twoTeamCombat([$monster], [$opponent]);
		$this->set('battlesJSON',json_encode([$result],JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP));
		$this->set('statuses',$this->fetchTable('Statuses')
            ->find()
            ->all()
            ->toList());
		$this->render('view');
	}
}