<?php
namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\ORM\Query;
use Cake\ORM\Query\SelectQuery;
use Cake\I18n\DateTime;

class StartDreamingCommand extends Command {

    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser {
        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io) {
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
    }
}
?>