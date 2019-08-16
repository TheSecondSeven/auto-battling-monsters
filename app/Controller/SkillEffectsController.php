<?php
App::uses('AppController', 'Controller');
/**
 * SkillEffects Controller
 *
 * @property SkillEffect $SkillEffect
 * @property PaginatorComponent $Paginator
 */
class SkillEffectsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session', 'Flash');
	
/**
 * Uses
 *
 * @var array
 */
	public $uses = array('SkillEffect','Skill','Status');
	
	private $aoe_value = 1.5;
	private $healing_value = 0.9;

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index($skill_id = null) {
		if (!$this->Skill->exists($skill_id)) {
			throw new NotFoundException(__('Invalid skill'));
		}
		$options = array('conditions' => array('Skill.' . $this->Skill->primaryKey => $skill_id));
		$this->set('skill', $this->Skill->find('first', $options));
		$this->SkillEffect->recursive = 0;
		$this->Paginator->settings = array(
	        'conditions' => [
				'SkillEffect.skill_id' => $skill_id
			]
	    );
		$this->set('skillEffects', $this->Paginator->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		if (!$this->SkillEffect->exists($id)) {
			throw new NotFoundException(__('Invalid skill effect'));
		}
		$options = array('conditions' => array('SkillEffect.' . $this->SkillEffect->primaryKey => $id));
		$this->set('skillEffect', $this->SkillEffect->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add($skill_id = null) {
		if (!$this->Skill->exists($skill_id)) {
			throw new NotFoundException(__('Invalid skill'));
		}
		$options = array('conditions' => array('Skill.' . $this->Skill->primaryKey => $skill_id));
		$skill = $this->Skill->find('first', $options);
		$this->set('skill', $skill);
		if ($this->request->is('post')) {
			$this->SkillEffect->create();
			$this->request->data['SkillEffect']['skill_id'] = $skill_id;
			if ($this->SkillEffect->save($this->request->data)) {
				$this->Flash->success(__('The skill effect has been saved.'));
				$this->calculateSkillValue($skill['Skill']['id']);
				return $this->redirect(array('controller' => 'skills', 'action' => 'view', $skill_id));
			} else {
				$this->Flash->error(__('The skill effect could not be saved. Please, try again.'));
			}
		}
		$skills = $this->SkillEffect->Skill->find('list');
		$skillEffects = [0 => 'Nothing'] + $this->SkillEffect->find('list', [
			'conditions' => [
				'SkillEffect.skill_id' => $skill_id,
				'SkillEffect.skill_effect_id' => 0
			]
		]);
		$statuses = [''=> 'N/A', 'random_buff' => 'Random Buff','all_buffs' => 'All Buffs', 'random_debuff' => 'Random Debuff', 'all_debuffs' => 'All Debuffs', 'random_buff_debuff' => 'Random Buff or Debuff', 'all_buffs_debuffs' => 'All Buffs and Debuffs'] + $this->Status->find('list', [
			'conditions' => [
				'Status.type !=' => 'Status'
			],
			'fields' => [
				'Status.class',
				'Status.name'
			]
		]);
		$this->set(compact('skills', 'skillEffects', 'statuses'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		if (!$this->SkillEffect->exists($id)) {
			throw new NotFoundException(__('Invalid skill effect'));
		}
		$options = array('conditions' => array('SkillEffect.' . $this->SkillEffect->primaryKey => $id));
		$skill_effect = $this->SkillEffect->find('first', $options);
		$options = array('conditions' => array('Skill.' . $this->Skill->primaryKey => $skill_effect['SkillEffect']['skill_id']));
		$skill = $this->Skill->find('first', $options);
		if ($this->request->is(array('post', 'put'))) {
			if ($this->SkillEffect->save($this->request->data)) {
				$this->Flash->success(__('The skill effect has been saved.'));
				$this->calculateSkillValue($skill['Skill']['id']);
				return $this->redirect(array('controller' => 'skills', 'action' => 'view', $skill['Skill']['id']));
			} else {
				$this->Flash->error(__('The skill effect could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('SkillEffect.' . $this->SkillEffect->primaryKey => $id));
			$this->request->data = $this->SkillEffect->find('first', $options);
		}
		$skills = $this->SkillEffect->Skill->find('list');
		$skillEffects = [0 => 'Nothing'] + $this->SkillEffect->find('list', [
			'conditions' => [
				'SkillEffect.skill_id' => $this->request->data['SkillEffect']['skill_id'],
				'SkillEffect.skill_effect_id' => 0
			]
		]);
		$statuses = [''=> 'N/A', 'random_buff' => 'Random Buff','all_buffs' => 'All Buffs', 'random_debuff' => 'Random Debuff', 'all_debuffs' => 'All Debuffs', 'random_buff_debuff' => 'Random Buff or Debuff', 'all_buffs_debuffs' => 'All Buffs and Debuffs'] + $this->Status->find('list', [
			'conditions' => [
				'Status.type !=' => 'Status'
			],
			'fields' => [
				'Status.class',
				'Status.name'
			]
		]);
		$this->set(compact('skills', 'skillEffects', 'statuses'));
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		$options = array('conditions' => array('SkillEffect.' . $this->SkillEffect->primaryKey => $id));
		$skill_effect = $this->SkillEffect->find('first', $options);
		$options = array('conditions' => array('Skill.' . $this->Skill->primaryKey => $skill_effect['SkillEffect']['skill_id']));
		$skill = $this->Skill->find('first', $options);
		$this->SkillEffect->id = $id;
		if (!$this->SkillEffect->exists()) {
			throw new NotFoundException(__('Invalid skill effect'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->SkillEffect->delete()) {
			$this->calculateSkillValue($skill['Skill']['id']);
			$this->Flash->success(__('The skill effect has been deleted.'));
		} else {
			$this->Flash->error(__('The skill effect could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
	
	public function initialValue() {
		$skills = $this->Skill->find('all', [
			'recursive' => -1
		]);
		foreach($skills as $skill) {
			$this->calculateSkillValue($skill['Skill']['id']);
		}
		exit;
	}
	
	public function admin_edit_skill($id = null) {
		if (!$this->Skill->exists($id)) {
			throw new NotFoundException(__('Invalid skill'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Skill->save($this->request->data)) {
				$this->calculateSkillValue($id);
				$this->Flash->success(__('The skill has been saved.'));
				return $this->redirect(array('controller' => 'skills', 'action' => 'view', $id));
			} else {
				$this->Flash->error(__('The skill could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Skill.' . $this->Skill->primaryKey => $id));
			$this->request->data = $this->Skill->find('first', $options);
		}
		$types = $this->Skill->Type->find('list');
		$this->set(compact('types'));	
	}
	
	
	private function calculateSkillValue($skill_id) {
		$skill = $this->Skill->find('first', [
			'conditions' => [
				'Skill.id' => $skill_id
			],
			'contain' => [
				'SkillEffect' => [
					'SecondarySkillEffect'
				]
			]
		]);
		if(!empty($skill['Skill']['id'])) {
			$total_points = 0;
			$total_points += (1 - $skill['Skill']['cast_time']) / 0.5 * 2;
			foreach($skill['SkillEffect'] as $skill_effect) {
				if($skill_effect['skill_effect_id'] == 0) {
					$total_points += $this->skilLEffectValue($skill_effect);
					
				}
			}
			$total_value = $total_points / ($skill['Skill']['cast_time'] + $skill['Skill']['down_time']);
			$this->Skill->id = $skill['Skill']['id'];
			$this->Skill->saveField('value', round($total_value,2));
		}
	}
	
	private function skillEffectTargetValue($skill_effect) {
		if($skill_effect['targets'] == 'Single Enemy') {
			return 1;
		}elseif($skill_effect['targets'] == 'All Enemies') {
			return 1.5;
		}elseif($skill_effect['targets'] == 'Self') {
			return 1;
		}elseif($skill_effect['targets'] == 'All Allies') {
			return 1.5;
		}elseif($skill_effect['targets'] == 'Everyone') {
			return 0.5;
		}else{
			return 1;
		}
	}
	
	private function skillEffectChance($skill_effect) {
		return $skill_effect['chance'] / 100;
	}
	
	private function skillEffectAverageAmount($skill_effect) {
		return ($skill_effect['amount_min'] + $skill_effect['amount_max']) / 2;
	}
	
	private function skillEffectValue($skill_effect) {
		if($skill_effect['effect'] == 'Random Amount') {
			$secondary_value = 0;
			foreach($skill_effect['SecondarySkillEffect'] as $secondary_skill_effect) {
				$secondary_value += $this->skillEffectValue($secondary_skill_effect);
			}
			return $secondary_value * $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * $this->skillEffectAverageAmount($skill_effect);
		}elseif($skill_effect['effect'] == 'Physical Damage') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * $this->skillEffectAverageAmount($skill_effect);
		}elseif($skill_effect['effect'] == 'Magical Damage') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * $this->skillEffectAverageAmount($skill_effect);
		}elseif($skill_effect['effect'] == 'True Damage') {
			$total_points = 1.15 * $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * $this->skillEffectAverageAmount($skill_effect);
		}elseif($skill_effect['effect'] == 'Leech') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * $this->skillEffectAverageAmount($skill_effect) * (1 + $this->healing_value);
		}elseif($skill_effect['effect'] == 'Heal') {
			$total_points = $this->healing_value * $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * $this->skillEffectAverageAmount($skill_effect);
		}elseif($skill_effect['effect'] == 'Heal Over Time') {
			$total_points = $this->healing_value * $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * $this->skillEffectAverageAmount($skill_effect) * $skill_effect['duration'];
		}elseif($skill_effect['effect'] == 'Poison') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * $this->skillEffectAverageAmount($skill_effect) * $skill_effect['duration'] * 2;
		}elseif($skill_effect['effect'] == 'Infect') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * 2;
		}elseif($skill_effect['effect'] == 'Bubble') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * 7;
		}elseif($skill_effect['effect'] == 'Burn') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * BURN_AMOUNT * BURN_DURATION / 2000 * 3;
		}elseif($skill_effect['effect'] == 'Stun') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * (10 + 5 * $skill_effect['duration']);
		}elseif($skill_effect['effect'] == 'Sleep') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * (10 + 2 * $skill_effect['duration']);
		}elseif($skill_effect['effect'] == 'Freeze') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * (10 + 3 * $skill_effect['duration']);
		}elseif($skill_effect['effect'] == 'Cleanse') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * 5;
		}elseif($skill_effect['effect'] == 'Purge') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * 2;
		}elseif($skill_effect['effect'] == 'Attack Up') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * $this->skillEffectAverageAmount($skill_effect) / 9 * $skill_effect['duration'];
		}elseif($skill_effect['effect'] == 'Attack Down') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * $this->skillEffectAverageAmount($skill_effect) / 8 * $skill_effect['duration'];
		}elseif($skill_effect['effect'] == 'Defense Up') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * $this->skillEffectAverageAmount($skill_effect) / 10 * $skill_effect['duration'];
		}elseif($skill_effect['effect'] == 'Defense Down') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * $this->skillEffectAverageAmount($skill_effect) / 8 * $skill_effect['duration'];
		}elseif($skill_effect['effect'] == 'Speed Up') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * $this->skillEffectAverageAmount($skill_effect) / 30 * $skill_effect['duration'];
		}elseif($skill_effect['effect'] == 'Speed Down') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * $this->skillEffectAverageAmount($skill_effect) / 60 * $skill_effect['duration'];
		}elseif($skill_effect['effect'] == 'Evade Up') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * $this->skillEffectAverageAmount($skill_effect) / 5 * $skill_effect['duration'];
		}elseif($skill_effect['effect'] == 'Evade Down') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * $this->skillEffectAverageAmount($skill_effect) / 25 * 10 * $skill_effect['duration'] / 6;
		}elseif($skill_effect['effect'] == 'Wet') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * 2;
		}elseif($skill_effect['effect'] == 'Delay') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * 10 * $this->skillEffectAverageAmount($skill_effect);
		}elseif($skill_effect['effect'] == 'Undying') {
			$total_points = $this->skillEffectTargetValue($skill_effect) * $this->skillEffectChance($skill_effect) * 1 * $skill_effect['duration'];
		}else{
			pr($skill_effect);
			return 0;
		}
		if(in_array($skill_effect['targets'],['Self','All Allies']) && in_array($skill_effect['effect'], ['Physical Damage','Magical Damage','True Damage','Leech','Poison','Infect','Burn','Stun','Sleep','Freeze','Attack Down','Defense Down','Speed Down','Evade Down','Wet','Delay'])) {
			$total_points *= -0.8;
		}
		if(in_array($skill_effect['targets'],['Single Enemy','All Enemies']) && !in_array($skill_effect['effect'], ['Physical Damage','Magical Damage','True Damage','Leech','Poison','Infect','Burn','Stun','Sleep','Freeze','Attack Down','Defense Down','Speed Down','Evade Down','Wet','Delay'])) {
			$total_points *= -0.8;
		}
		if(!empty($skill_effect['SecondarySkillEffect'])) {
			foreach($skill_effect['SecondarySkillEffect'] as $secondary_skill_effect) {
				$total_points += $this->skillEffectChance($skill_effect) * $this->skillEffectValue($secondary_skill_effect);
			}
		}
		return $total_points;
	}
}
