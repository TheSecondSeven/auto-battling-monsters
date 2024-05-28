<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;

class GauntletRunBattlesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('Timestamp');
		$this->belongsTo('GauntletRuns');
		$this->belongsTo('Opponents')
            ->setForeignKey('opponent_id')
			->setClassName('Monsters');
    }
}