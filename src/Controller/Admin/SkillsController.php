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
class SkillsController extends AppController
{
	private $aoe_value = 1.5;
	private $healing_value = 0.9;

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
        if(!empty($rarity)) $where['Skills.rarity'] = $rarity;
        if(!empty($type_id)) $where['Skills.type_id'] = $type_id;
        if(!empty($name)) $where['Skills.name LIKE'] = '%'.$name.'%';
		$this->paginate = [
			'Skills' => [
				'order' => [
					'type_id' => 'ASC',
					'rarity' => 'DESC',
					'value' => 'DESC'
				],
			]
		];
		$skills = $this->paginate($this->Skills
            ->find()
			->where($where)
			->contain([
				'Types'
			])
        );
        $rarities = $this->Skills->rarities();

		$types = $this->Skills->Types->find('list')
            ->where([])
            ->all();
        $this->set(compact(['skills','rarities','types']));
	}

	public function view($id = null) {
		$this->set('skill', $this->Skills
            ->find()
            ->where([
                'Skills.id' => $id
            ])
            ->contain([
                'Types',
                'SkillEffects' => [
                    'SecondarySkillEffects'
                ]
            ])
			->first()
        );
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
    
	public function create() {
        $skill = $this->Skills->newEmptyEntity();
		if ($this->request->is('post')) {

            $skill = $this->Skills->patchEntity($skill, $this->request->getData());
			if ($this->Skills->save($skill)) {
				$this->calculateSkillValue($skill->id);
				$this->Flash->success(__('The skill has been created.'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('The skill could not be saved. Please, try again.'));
			}
		}
		$types = $this->Skills->Types->find('list')
            ->where([])
            ->all();
		$this->set(compact('skill','types'));
	}
    
	public function update($id = null) {
		$skill = $this->Skills
            ->find()
            ->where([
                'Skills.id' => $id
            ])
            ->firstOrFail();
		if ($this->request->is(array('post', 'put'))) {
            $skill = $this->Skills->patchEntity($skill, $this->request->getData());
			if ($this->Skills->save($skill)) {
				$this->calculateSkillValue($skill->id);
				$this->Flash->success(__('The skill has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The skill could not be saved. Please, try again.'));
			}
		}
		$types = $this->Skills->Types->find('list')
            ->where([])
            ->all();
		$this->set(compact('skill','types'));
	}

	public function delete($id = null) {
		$skill = $this->Skills
            ->find()
            ->where([
                'Skills.id' => $id
            ])
            ->firstOrFail();
		if ($this->Skills->delete($skill)) {
			$this->Flash->success(__('The skill has been deleted.'));
		} else {
			$this->Flash->error(__('The skill could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}


	public function addSkillEffect($skill_id = null) {
		$skill = $this->Skills
            ->find()
            ->where([
                'Skills.id' => $skill_id
            ])
            ->firstOrFail();
        $skill_effect = $this->Skills->SkillEffects->newEntity(['skill_id' => $skill_id]);
		if ($this->request->is('post')) {
            $skill_effect = $this->Skills->SkillEffects->patchEntity($skill_effect, $this->request->getData());
			if ($this->Skills->SkillEffects->save($skill_effect)) {
				$this->Flash->success(__('The skill effect has been added.'));
				$this->calculateSkillValue($skill_id);
				return $this->redirect(array('controller' => 'skills', 'action' => 'view', $skill_id));
			} else {
				$this->Flash->error(__('The skill effect could not be saved. Please, try again.'));
			}
		}
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
		$effects = $this->Skills->SkillEffects->effects();
		$targets = $this->Skills->SkillEffects->targets();
		$statuses = [''=> 'N/A', 'random_buff' => 'Random Buff','all_buffs' => 'All Buffs', 'random_debuff' => 'Random Debuff', 'all_debuffs' => 'All Debuffs', 'random_buff_debuff' => 'Random Buff or Debuff', 'all_buffs_debuffs' => 'All Buffs and Debuffs'] + $status_effects_list;
		$this->set(compact('skill_effect','effects','targets','statuses'));
	}

	public function updateSkillEffect($skill_id, $skill_effect_id) {
		$skill_effect = $this->Skills->SkillEffects
            ->find()
            ->where([
                'SkillEffects.id' => $skill_effect_id
            ])
            ->firstOrFail();
		if ($this->request->is(array('post', 'put'))) {
            $skill_effect = $this->Skills->SkillEffects->patchEntity($skill_effect, $this->request->getData());
			if ($this->Skills->SkillEffects->save($skill_effect)) {
				$this->Flash->success(__('The skill effect has been updated.'));
				$this->calculateSkillValue($skill_id);
				return $this->redirect(array('controller' => 'skills', 'action' => 'view', $skill_id));
			} else {
				$this->Flash->error(__('The skill effect could not be saved. Please, try again.'));
			}
		}
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
		$effects = $this->Skills->SkillEffects->effects();
		$targets = $this->Skills->SkillEffects->targets();
		$statuses = [''=> 'N/A', 'random_buff' => 'Random Buff','all_buffs' => 'All Buffs', 'random_debuff' => 'Random Debuff', 'all_debuffs' => 'All Debuffs', 'random_buff_debuff' => 'Random Buff or Debuff', 'all_buffs_debuffs' => 'All Buffs and Debuffs'] + $status_effects_list;
		$this->set(compact('skill_effect','effects','targets','statuses'));
	}

	public function deleteSkillEffect($skill_id, $id = null) {
		$skill_effect = $this->Skills->SkillEffects
            ->find()
            ->where([
                'SkillEffects.id' => $id
            ])
            ->firstOrFail();
		if ($this->Skills->SkillEffects->delete($skill_effect)) {
			$this->calculateSkillValue($skill_id);
			$this->Flash->success(__('The skill effect has been deleted.'));
		} else {
			$this->Flash->error(__('The skill effect could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'view', $skill_id));
	}
	public function addSecondarySkillEffect($skill_id, $skill_effect_id) {
		$skill = $this->Skills
            ->find()
            ->where([
                'Skills.id' => $skill_id
            ])
            ->firstOrFail();
        $skill_effect = $this->Skills->SkillEffects->newEntity(['skill_effect_id' => $skill_effect_id]);
		if ($this->request->is('post')) {
            $skill_effect = $this->Skills->SkillEffects->patchEntity($skill_effect, $this->request->getData());
			if ($this->Skills->SkillEffects->save($skill_effect)) {
				$this->Flash->success(__('The secondary skill effect has been added.'));
				$this->calculateSkillValue($skill_id);
				return $this->redirect(array('controller' => 'skills', 'action' => 'view', $skill_id));
			} else {
				$this->Flash->error(__('The secondary skill effect could not be saved. Please, try again.'));
			}
		}
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
		$effects = $this->Skills->SkillEffects->effects();
		$targets = $this->Skills->SkillEffects->targets();
		$statuses = [''=> 'N/A', 'random_buff' => 'Random Buff','all_buffs' => 'All Buffs', 'random_debuff' => 'Random Debuff', 'all_debuffs' => 'All Debuffs', 'random_buff_debuff' => 'Random Buff or Debuff', 'all_buffs_debuffs' => 'All Buffs and Debuffs'] + $status_effects_list;
		$this->set(compact('skill_effect','effects','targets','statuses'));
	}
	
	public function calculateSkillValues() {
		$skills = $this->Skills
            ->find()
            ->all()
            ->toList();
		foreach($skills as $skill) {
			$this->calculateSkillValue($skill['Skill']['id']);
		}
		exit;
	}
	
	private function calculateSkillValue($skill_id) {
		$skill = $this->Skills
            ->find()
            ->where([
				'Skills.id' => $skill_id
			])
            ->contain([
				'SkillEffects' => [
					'SecondarySkillEffects'
				]
			])
            ->firstOrFail();
        $total_points = 0;
        $total_points += (1 - $skill->cast_time) / 0.5 * 2;
        foreach($skill->skill_effects as $skill_effect) {
            $total_points += $this->skillEffectValue($skill_effect);
        }
        $total_value = $total_points / ($skill->cast_time + $skill->down_time);
        $skill->value = $total_value;
        $this->Skills->save($skill);
        return true;
	}
	
	private function skillEffectTargetValue($skill_effect) {
		if($skill_effect->targets == 'Single Enemy') {
			return 1;
		}elseif($skill_effect->targets == 'All Enemies') {
			return 1.5;
		}elseif($skill_effect->targets == 'Self') {
			return 1;
		}elseif($skill_effect->targets == 'All Allies') {
			return 1.5;
		}elseif($skill_effect->targets == 'Everyone') {
			return 0.5;
		}else{
			return 1;
		}
	}
	
	private function skillEffectChance($skill_effect) {
		return $skill_effect->chance / 100;
	}
	
	private function skillEffectAverageAmount($skill_effect) {
		return ($skill_effect->amount_min + $skill_effect->amount_max) / 2;
	}
	
	private function skillEffectValue($skill_effect, $primary_skill_effect = null) {
		if(!empty($primary_skill_effect) && $skill_effect->targets == 'Same as Primary Effect')
			$skill_effect->targets = $primary_skill_effect->targets;
		if($skill_effect->effect == 'Random Amount') {
			$secondary_value = 0;
			foreach($skill_effect->secondary_skill_effects as $secondary_skill_effect) {
				$secondary_value += $this->skillEffectValue($secondary_skill_effect, $skill_effect);
			}
			return $secondary_value * $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * $this->skillEffectAverageAmount($skill_effect);
		}elseif($skill_effect->effect == 'Physical Damage') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * $this->skillEffectAverageAmount($skill_effect);
		}elseif($skill_effect->effect == 'Magical Damage') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * $this->skillEffectAverageAmount($skill_effect);
		}elseif($skill_effect->effect == 'True Damage') {
			$total_points = 1.15 * $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * $this->skillEffectAverageAmount($skill_effect);
		}elseif($skill_effect->effect == 'Leech') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * $this->skillEffectAverageAmount($skill_effect) * (1 + $this->healing_value);
		}elseif($skill_effect->effect == 'Heal') {
			$total_points = $this->healing_value * $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * $this->skillEffectAverageAmount($skill_effect);
		}elseif($skill_effect->effect == 'Heal Over Time') {
			$total_points = $this->healing_value * $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * $this->skillEffectAverageAmount($skill_effect) * $skill_effect->duration;
		}elseif($skill_effect->effect == 'Poison') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * $this->skillEffectAverageAmount($skill_effect) * $skill_effect->duration * 2;
		}elseif($skill_effect->effect == 'Infect') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * 2;
		}elseif($skill_effect->effect == 'Bubble') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * 7;
		}elseif($skill_effect->effect == 'Burn') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * BURN_AMOUNT * BURN_DURATION / 2000 * 3;
		}elseif($skill_effect->effect == 'Stun') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * (10 + 5 * $skill_effect->duration);
		}elseif($skill_effect->effect == 'Sleep') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * (10 + 2 * $skill_effect->duration);
		}elseif($skill_effect->effect == 'Freeze') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * (10 + 3 * $skill_effect->duration);
		}elseif($skill_effect->effect == 'Cleanse') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * 5;
		}elseif($skill_effect->effect == 'Purge') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * 2;
		}elseif($skill_effect->effect == 'Attack Up') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * $this->skillEffectAverageAmount($skill_effect) / 9 * $skill_effect->duration;
		}elseif($skill_effect->effect == 'Attack Down') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * $this->skillEffectAverageAmount($skill_effect) / 8 * $skill_effect->duration;
		}elseif($skill_effect->effect == 'Defense Up') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * $this->skillEffectAverageAmount($skill_effect) / 10 * $skill_effect->duration;
		}elseif($skill_effect->effect == 'Defense Down') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * $this->skillEffectAverageAmount($skill_effect) / 8 * $skill_effect->duration;
		}elseif($skill_effect->effect == 'Speed Up') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * $this->skillEffectAverageAmount($skill_effect) / 30 * $skill_effect->duration;
		}elseif($skill_effect->effect == 'Speed Down') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * $this->skillEffectAverageAmount($skill_effect) / 60 * $skill_effect->duration;
		}elseif($skill_effect->effect == 'Evade Up') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * $this->skillEffectAverageAmount($skill_effect) / 5 * $skill_effect->duration;
		}elseif($skill_effect->effect == 'Evade Down') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * $this->skillEffectAverageAmount($skill_effect) / 25 * 10 * $skill_effect->duration / 6;
		}elseif($skill_effect->effect == 'Wet') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * 2;
		}elseif($skill_effect->effect == 'Delay') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * 10 * $this->skillEffectAverageAmount($skill_effect);
		}elseif($skill_effect->effect == 'Undying') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * 1 * $skill_effect->duration;
		}else{
			pr($skill_effect);
			return 0;
		}
		if(in_array($skill_effect->targets,['Self','All Allies']) && in_array($skill_effect->effect, ['Physical Damage','Magical Damage','True Damage','Leech','Poison','Infect','Burn','Stun','Sleep','Freeze','Attack Down','Defense Down','Speed Down','Evade Down','Wet','Delay'])) {
			$total_points *= -0.8;
		}
		if(in_array($skill_effect->targets,['Single Enemy','All Enemies']) && !in_array($skill_effect->effect, ['Physical Damage','Magical Damage','True Damage','Leech','Poison','Infect','Burn','Stun','Sleep','Freeze','Attack Down','Defense Down','Speed Down','Evade Down','Wet','Delay'])) {
			$total_points *= -0.8;
		}
		if(!empty($skill_effect->secondary_skill_effects)) {
			foreach($skill_effect->secondary_skill_effects as $secondary_skill_effect) {
				$total_points += $this->skillEffectChance($skill_effect) * $this->skillEffectValue($secondary_skill_effect);
			}
		}
		return $total_points;
	}

}