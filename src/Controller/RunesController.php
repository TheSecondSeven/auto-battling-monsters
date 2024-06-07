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
class RunesController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authentication->addUnauthenticatedActions([]);
    }

    public function myRunes() {
		$this->set('runes', $this->paginate($this->Runes
            ->find()
            ->where(['Runes.user_id' => $this->user->id])
            ->contain([
                'Monsters',
                'Types'
            ])
		));
	}

    public function create() {
		if($this->user->gold < 25) {
			$this->Flash->error(__('You do not have 25 Gold. Battle in the Gauntlet to get some!'));
			return $this->redirect(['action' => 'my-runes']);
		}
        $rune = $this->Runes->newEmptyEntity();
		if ($this->request->is('post')) {

            $rune = $this->Runes->patchEntity($rune, [
                'type_id' => $this->request->getData()['type_id'],
                'user_id' => $this->user->id,
                $this->request->getData()['first_upgrade'] => 1
            ]);
			if ($this->Runes->save($rune)) {
				$this->Runes->Users->removeGoldFromUser($this->user->id, 25, 'Created a Rune');
				$this->Flash->success(__('The rune has been created.'));
				return $this->redirect(['action' => 'my-runes']);
			} else {
				$this->Flash->error(__('The rune could not be saved. Please, try again.'));
			}
		}
		$types = $this->Runes->Types->find('list')
            ->where()
            ->all();
		$upgrade_options = $rune->upgrades();
		$this->set(compact('rune','types','upgrade_options'));
	}
	
	public function upgrade($id = null) {
        $user = $this->Authentication->getResult()->getData();
		$rune = $this->Runes
            ->find()
			->where([
				'Runes.id' => $id,
				'Runes.user_id' => $this->user->id
			])
			->contain([
				'Types'
			])
            ->firstOrFail();
		
		if ($this->request->is(array('post', 'put'))) {
			//validate cost
			if($rune->get('current_level') < $rune->level) {
				$cost = 0;
			}else{
				$cost = pow(5, $rune->level + 1);
			}
			if($cost <= $this->user->rune_shards) {
				if($rune->get('current_level') == $rune->level) {
					$rune->level += 1;
				}
				$choice = $this->request->getData()['upgrade'];
				$rune->$choice += 1;
				if ($this->Runes->save($rune)) {
					if($cost > 0) $this->Runes->Users->removeRuneShardsFromUser($this->user->id, $cost, 'Upgrading a Rune');
					$this->Flash->success(__('The rune has been upgraded.'));
					return $this->redirect(['action' => 'my-runes']);
				} else {
					$this->Flash->error(__('The rune could not be upgraded. Please, try again.'));
				}
			}else{
				$this->Flash->error(__('You do not have enough Rune Shards('.$cost.') for this Upgrade.'));
			}
		}
		$types = $this->Runes->Types->find('list')
            ->where()
            ->all();
		$upgrades = $rune->upgrades($rune->type->name);
        $upgrade_options = [];
		foreach($upgrades as $field=>$upgrade) {
			if($rune->$field < 5 && ($rune->unlock_type == 0 || $field != 'unlock_type')) {
				$upgrade_options[$field] = $upgrade;
			}
		}

		if($rune->get('current_level') < $rune->level) {
			$cost = 0;
		}else{
			$cost = pow(5, $rune->level + 1);
		}
		
		$this->set(compact('rune','types','upgrade_options','cost'));
		
	}

}