<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
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
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
 
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
	Router::connect('/', array('controller' => 'users', 'action' => 'my_monsters'));
	Router::connect('/register/*', array('controller' => 'users', 'action' => 'register'));
	Router::connect('/my-monsters/*', array('controller' => 'users', 'action' => 'my_monsters'));
	
	Router::connect('/edit-monster-details/*', array('controller' => 'monsters', 'action' => 'edit'));
	Router::connect('/edit-monster-skills/*', array('controller' => 'monsters', 'action' => 'edit_skills'));
	Router::connect('/edit-monster-runes/*', array('controller' => 'monsters', 'action' => 'edit_runes'));
	Router::connect('/my-skills/*', array('controller' => 'users', 'action' => 'my_skills'));
	Router::connect('/view-skill/*', array('controller' => 'skills', 'action' => 'view'));
	Router::connect('/my-ultimates/*', array('controller' => 'users', 'action' => 'my_ultimates'));
	Router::connect('/view-ultimate/*', array('controller' => 'ultimates', 'action' => 'view'));
	Router::connect('/my-runes/*', array('controller' => 'users', 'action' => 'my_runes'));
	Router::connect('/create-rune/*', array('controller' => 'runes', 'action' => 'create'));
	Router::connect('/view-rune/*', array('controller' => 'runes', 'action' => 'view'));
	Router::connect('/new/', array('controller' => 'users', 'action' => 'new_stuff'));
	Router::connect('/view-gauntlet-battles/*', array('controller' => 'gauntlet_runs', 'action' => 'view_battles'));
	Router::connect('/view-gauntlet-results/*', array('controller' => 'gauntlet_runs', 'action' => 'view_results'));
	Router::connect('/choose-reward/*', array('controller' => 'gauntlet_runs', 'action' => 'choose_reward'));
	Router::connect('/battle-practice/*', array('controller' => 'battle', 'action' => 'practice'));
	Router::connect('/leaderboard/*', array('controller' => 'monsters', 'action' => 'leaderboard'));
/**
 * ...and connect the rest of 'Pages' controller's URLs.
 */
	Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
	require CAKE . 'Config' . DS . 'routes.php';
