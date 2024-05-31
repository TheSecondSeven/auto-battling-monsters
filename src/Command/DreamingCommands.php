<?php
namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

class DreamingCommand extends Command
{
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {

        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io)
    {
        $users = $this->fetchTable('Users')
            ->find()
            ->where([
                'Users.dreaming_since >' => date('Y-m-d H:i:s',strtotime(('+2 hours'))),
                'Users.dreaming_since <=' => date('Y-m-d H:i:s',strtotime(('+26 hours')))
            ])
            ->contain('Monsters', function (SelectQuery $q) {
                return $q
                    ->where([
                        'Monsters.in_gauntlet_run' => 1
                    ]);
            })
            ->all();
        foreach($users as $user) {
            //chance to earn gold
            if(rand(1,600) <= 10) {
                //they earned gold!
                $user->dreamt_gold += DREAMT_GOLD_PER_HOUR;
            }
            $active_slots = $user->active_monster_limit - count($user->monsters);
            for($i=0;$i<$active_slots;$i++) {
                //chance to earn rune shards or gem
                $roll = rand(1,600);
                if($roll <= 2) {
                    $user->dreamt_gems += DREAMT_GEMS_PER_FIVE_HOURS_PER_ACTIVE_MONSTER_SLOT;
                }elseif($roll <= 12) {
                    $user->dreamt_rune_shards += DREAMT_RUNE_SHARDS_PER_HOUR_PER_ACTIVE_MONSTER_SLOT;
                }
            }
            $this->fetchTable('Users')->save($user);
        }
    }
}
?>