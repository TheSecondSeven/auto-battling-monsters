<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		https://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
	public $components = [
		'DebugKit.Toolbar',
		'Auth' => [
			'authorize' => 'Controller',
	        'loginAction' => [
	            'controller' => 'users',
	            'action' => 'login'
	        ],
	        'authError' => 'Please login?',
	        'authenticate' => [
	            'Form' => [
	                'fields' => [
	                  'username' => 'email', //Default is 'username' in the userModel
	                  'password' => 'password'  //Default is 'password' in the userModel
	                ],
	                'passwordHasher' => [
	                    'className' => 'Simple',
	                    'hashType' => 'sha256'
	                ]
	            ]
	        ]
	    ],
	    'Combat',
	    'Session',
	    'Flash',
	    'RequestHandler'
	];
	public $uses = [
		'GauntletRun',
		'Monster',
		'User'
	];
	
    public function isAuthorized($user = null) {
        if (!empty($this->request->prefix) && $this->request->prefix == 'admin') {
            return (bool)($user['type'] === 'Admin');
        }
        if (!empty($this->request->prefix) && $this->request->prefix == 'api') {
            return true;
        }
        if($user) {
        	return true;
		}else{
			return false;
		}
    }
    
	public function beforeFilter() {
		parent::beforeFilter();
		
        if (!empty($this->request->prefix) && $this->request->prefix == 'admin') {
	        $this->layout = 'admin';
	    }
	    $user_id = $this->Auth->user('id');
	    
	    if(!empty($user_id)) {
		    $this->set('user', $this->User->findById($user_id));
	    }
	    //maybe only do this if they have a cookie saying a gauntlet was started
		if($this->params['action'] != 'my_monsters' && $this->params['action'] != 'complete_run' && !$this->Session->check('Message.flash')) {
			if(!empty($user_id)) {
				//check for gauntlet ready to complete
				$monster = $this->Monster->find('first', [
					'conditions' => [
						'Monster.user_id' => $user_id,
						'Monster.in_gauntlet_run' => 1,
						'Monster.in_gauntlet_run_until <= "'.date('Y-m-d H:i:s').'"'
					]
				]);
			    if(!empty($monster['Monster']['id'])) {
				    $this->Flash->success(__($monster['Monster']['name'].' has completed the Gauntlet!'));
			    }
			}
	    }
	    /*
		if($this->params['action'] != 'view_results' && $this->params['action'] != 'choose_reward' && !$this->Session->check('Message.flash')) {
		    if(!empty($user_id)) {
			    //check for unpicked rewards
			    $gauntlet_run = $this->GauntletRun->find('first', [
				    'conditions' => [
					    'GauntletRun.user_id' => $user_id,
					    'GauntletRun.number_of_rewards_chosen < GauntletRun.number_of_reward_picks'
				    ]
			    ]);
			    if(!empty($gauntlet_run['GauntletRun']['id'])) {
				    $this->Flash->rewards_to_pick(__('You still have rewards to pick!'));
				    $this->set('gauntlet_run_with_rewards', $gauntlet_run);
			    }
		    }
	    }*/
	    
	}
}
