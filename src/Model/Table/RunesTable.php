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
    public function addRuneToUser($user_id, $types = [], $level = 0) {
        if(empty($types)) {
            $types = [1,2,3,5,6,8,10];
        }
        $type_id = $types[rand(0,count($types) - 1)];
        
		$rune = $this->newEntity(
			[
				'user_id' => $user_id,
                'type_id' => $type_id,
                'level' => $level
			],
			['validate' => false]
		);
		$this->save($rune);
		return $rune;
	}
}