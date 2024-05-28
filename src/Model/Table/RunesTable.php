<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;

class RunesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('Timestamp');
        $this->belongsTo('Monsters')
			->setForeignKey('in_use_by_monster_id');
        $this->belongsTo('Types');
        $this->belongsTo('Users');
    }
}