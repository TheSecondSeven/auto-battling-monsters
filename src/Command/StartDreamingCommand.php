<?php
namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

class StartDreamingCommand extends Command
{
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {

        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $users = $this->fetchTable('Users')
            ->find()
            ->where([
                'Users.dreaming_since IS NULL',
                'Users.auto_dream_time' => date('H:i:00')
            ])
            ->all();
        foreach($users as $user) {
		    $user->dreaming_since = new DateTime();
            $this->fetchTable('Users')->save($user);
        }
        return true;
    }
}