<?php
App::uses('AppController', 'Controller');
/**
 * Skills Controller
 *
 * @property Skill $Skill
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 * @property FlashComponent $Flash
 */
class TheMindController extends AppController {

/**
 * Components
 *
 * @var array
 */
	
	public $uses = [
		'TheMindNumber',
		'User'
	];

	function index() {
		$user_id = $this->Auth->user('id');
		if(empty($user_id))
			$this->redirect('/');
	}
	
	function start_round($number_of_cards) {
		$user_id = $this->Auth->user('id');
		if(empty($user_id))
			$this->redirect('/');
		$this->TheMindNumber->query('TRUNCATE the_mind_numbers');
		for($i=0;$i < $number_of_cards; $i++)
			$this->create_number($user_id);
		$this->redirect('/TheMind/view_hand');
	}
	
	
	private function create_number($user_id) {
		$number_found = false;
		while(!$number_found) {
			$number = rand(1, 100);
			//check if it exists
			$exists = $this->TheMindNumber->findByNumber($number);
			if(empty($exists)) {
				$this->TheMindNumber->create();
				$tmn['TheMindNumber'] = [
					'id' => null,
					'user_id' => $user_id,
					'number' => $number
				];
				if($this->TheMindNumber->save($tmn))
					$number_found = true;
			}
		}
		return true;
	}
	
	function join_round() {
		$user_id = $this->Auth->user('id');
		if(empty($user_id))
			$this->redirect('/');
		//find a user
		$a_number = $this->TheMindNumber->find('first');
		if(!empty($a_number)) {
			//find number_of_cards
			$number_of_cards = $this->TheMindNumber->find('count', [
				'conditions' => [
					'TheMindNumber.user_id' => $a_number['TheMindNumber']['user_id']
				]
			]);
			
			for($i=0;$i < $number_of_cards; $i++)
				$this->create_number($user_id);
				
			$this->redirect('/TheMind/view_hand');
		}else{
			$this->redirect('/TheMind/index');
		}
	}
	
	function view_hand() {
		
		$user_id = $this->Auth->user('id');
		if(empty($user_id))
			$this->redirect('/');
		$this->TheMindNumber->bindModel([
			'belongsTo' => [
				'User'
			]
		]);
		$played_numbers = $this->TheMindNumber->find('all', [
			'conditions' => [
				'OR' => [
					'TheMindNumber.played' => 1,
					'TheMindNumber.skipped' => 1,
					'TheMindNumber.destroyed' => 1
				]
			],
			'order' => [
				'TheMindNumber.number ASC'
			]
		]);
		$this->set('played_numbers', $played_numbers);
		
		
		$numbers = $this->TheMindNumber->find('all', [
			'conditions' => [
				'TheMindNumber.user_id' => $user_id,
				'TheMindNumber.played' => 0,
				'TheMindNumber.skipped' => 0,
				'TheMindNumber.destroyed' => 0,
			],
			'order' => [
				'TheMindNumber.number ASC'
			]
		]);
		$this->set('numbers_in_hand', $numbers);
		
		$this->set('user_id', $user_id);
	}
	
	function play_number($number) {
		$the_mind_number = $this->TheMindNumber->findByNumber($number);
		if(!empty($the_mind_number['TheMindNumber']['id'])) {
			$this->TheMindNumber->id = $the_mind_number['TheMindNumber']['id'];
			$this->TheMindNumber->saveField('played', 1);
			
			//set all other numbers to skipped
			$skipped_numbers = $this->TheMindNumber->find('all', [
				'conditions' => [
					'TheMindNumber.number <' => $number,
					'TheMindNumber.played' => 0,
					'TheMindNumber.skipped' => 0,
					'TheMindNumber.destroyed' => 0,
				]
			]);
			if(!empty($skipped_numbers)) {
				$this->Flash->error(__('You went out of order! You lost a life.'));
				foreach($skipped_numbers as $a_number) {
					$this->TheMindNumber->id = $a_number['TheMindNumber']['id'];
					$this->TheMindNumber->saveField('skipped', 1);
				}
			}else{
				$this->Flash->success(__('You were the next number! Good Job.'));
			}
			$this->redirect('/TheMind/view_hand');
		}else{
			$this->redirect('/TheMind/index');
		}
	}
	
	function destroy_numbers() {
		$numbers = $this->TheMindNumber->find('all', [
			'conditions' => [
				'TheMindNumber.played' => 0,
				'TheMindNumber.skipped' => 0,
				'TheMindNumber.destroyed' => 0,
			],
			'order' => [
				'TheMindNumber.number ASC'
			]
		]);
		$destroyed_for_user = [];
		foreach($numbers as $number) {
			if(!in_array($number['TheMindNumber']['user_id'], $destroyed_for_user)) {
				$this->TheMindNumber->id = $number['TheMindNumber']['id'];
				$this->TheMindNumber->saveField('destroyed', 1);
				$destroyed_for_user[] = $number['TheMindNumber']['user_id'];
			}
		}
		$this->redirect('/TheMind/view_hand');
	}
	
}
