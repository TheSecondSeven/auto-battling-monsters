<?php
namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;

class StartDreamingCommand extends Command {

    public function execute(Arguments $args, ConsoleIo $io) {
        $io->out('Hello world.');
        $this->loadModel('Users');
        $users = $this->fetchTable('Users')
            ->find()
            ->where([
                'Users.dreaming_since IS NULL',
                'Users.auto_dream_time' => date('H:i:00')
            ])
            ->all();
        foreach($users as $user) {
		    $user->dreaming_since = new DateTime();
            $this->Users->save($user);
        }
        return true;
    }
}
?>