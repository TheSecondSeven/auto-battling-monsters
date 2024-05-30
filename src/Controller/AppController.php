<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\ORM\Query\SelectQuery;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/4/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     */

    var $user = null;

    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Flash');
        $this->loadComponent('Authentication.Authentication');

        /*
         * Enable the following component for recommended CakePHP form protection settings.
         * see https://book.cakephp.org/4/en/controllers/components/form-protection.html
         */
        //$this->loadComponent('FormProtection');
        if(!empty($this->Authentication->getResult()->getData()->id)) {
            $user_id = $this->Authentication->getResult()->getData()->id;
            $this->user = $this->fetchTable('Users')
                ->find()
                ->where([
                    'Users.id' => $user_id
                ])
                ->contain('Monsters', function (SelectQuery $q) use ($user_id) {
                    return $q
                        ->where([
                            'Monsters.user_id' => $user_id,
                            'Monsters.in_gauntlet_run' => 1
                        ]);
                })
                ->first();
            
            $this->user->total_gauntlet_runs_today = $this->fetchTable('GauntletRuns')
                ->find()
                ->where([
                    'GauntletRuns.user_id' => $this->user->id,
                    'GauntletRuns.created >=' => date('Y-m-d 00:00:00')
                ])
                ->all()
                ->count() + count($this->user->monsters);
            if(!empty($this->user->dreaming_since)) {
                $unix = (int)$this->user->dreaming_since->toUnixString();
                $difference = time() - $unix - 3600;
                //we have rested for an hour, time to start earning gold.
                if($difference > 0) {
                    if($difference > 48 * 3600) $difference = 48 * 3600;
                    $this->user->dreamt_gold = round(DREAMT_GOLD_PER_HOUR * ($difference / 3600));
                    $active_slots = $this->user->active_monster_limit - count($this->user->monsters);
                    if($active_slots > 0) $this->user->dreamt_rune_shards = round(DREAMT_RUNE_SHARDS_PER_HOUR_PER_ACTIVE_MONSTER_SLOT * $active_slots * ($difference / 3600));
                }
            }
            $this->set('user', $this->user);
        }
    }
}
