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
		if($this->user->rune_shards < 5) {
			$this->Flash->error(__('You do not have 5 Rune Shards. Battle in the Gauntlet to get some!'));
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
                $this->user = $this->Runes->Users->patchEntity($this->user, ['rune_shards' => $this->user->rune_shards - 5], ['validate' => false]);
				$this->Runes->Users->save($this->user);
				$this->Authentication->setIdentity($this->user);
				$this->Flash->success(__('The rune has been created.'));
				return $this->redirect(['action' => 'my-runes']);
			} else {
				$this->Flash->error(__('The rune could not be saved. Please, try again.'));
			}
		}
		$types = $this->Runes->Types->find('list')
            ->where([
				'Types.name != "Neutral"'
			])
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
			$cost = pow(5, $rune->level + 1);
            $upgrade = $this->request->getData()['upgrade'];
			if($rune->$upgrade > 0) {
				$cost += pow(5, $rune->$upgrade);
			}
			if($cost <= $this->user->rune_shards) {
				$rune->level += 1;
				$choice = $this->request->getData()['upgrade'];
				$rune->$choice += 1;
				if ($this->Runes->save($rune)) {
                    $this->user = $this->Runes->Users->patchEntity($this->user, ['rune_shards' => $this->user->rune_shards - $cost], ['validate' => false]);
					$this->Runes->Users->save($this->user);
					$this->Authentication->setIdentity($this->user);
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
            ->where([
				'Types.name != "Neutral"'
			])
            ->all();
		$upgrades = $rune->upgrades($rune->type->name);
        $upgrade_options = [];
		foreach($upgrades as $field=>$upgrade) {
			if($rune->$field < 5 && ($rune->unlock_type == 0 || $field != 'unlock_type')) {
				$cost = pow(5, $rune->level + 1);
				if($rune->$field > 0) {
					$cost += pow(5, $rune->$field);
				}
				$upgrade_options[$field] = $upgrade.' for '.$cost.' Rune Shards';
			}
		}
		
		$this->set(compact('rune','types','upgrade_options'));
		
	}

}