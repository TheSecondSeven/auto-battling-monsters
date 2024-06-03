<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;

class UserQuestRewardsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('Timestamp');
        $this->belongsTo('Quests');
        $this->belongsTo('Users');
		$this->belongsTo('Skills');
		$this->belongsTo('Ultimates');
		$this->belongsTo('Types');
		$this->belongsTo('SecondaryTypes')
			->setForeignKey('secondary_type_id')
			->setClassName('Types');
    }
}