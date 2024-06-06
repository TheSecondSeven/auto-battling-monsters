<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;

class UserUltimatesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('Timestamp');
    }
    public function addUltimateToUser($user_id, $ultimate_id) {
        $already_own = $this
            ->find()
            ->where([
                'UserUltimates.ultimate_id' => $ultimate_id,
                'UserUltimates.user_id' => $user_id
            ])
            ->first();
        if(!empty($already_own->id))
            return null;

		$user_ultimate = $this->newEntity(
			[
				'user_id' => $user_id,
				'ultimate_id' => $ultimate_id
			],
			['validate' => false]
		);
		$this->save($user_ultimate);
		return $user_ultimate->id;
	}
    public function addRandomUltimateToUser($user_id, $rarity=null, $types = []) {
        $conditions = [];
        if(!empty($rarity))
            $conditions['Ultimates.rarity'] = $rarity;
        if(!empty($types))
            $conditions['Ultimates.type_id IN'] = $types;
        $random_ultimate = $this->Ultimates
            ->find()
            ->where($conditions)
            ->notMatching('UserUltimates', function ($q) use ($user_id) {
                return $q->where(['UserUltimates.user_id' => $user_id]);
            })
            ->order([
                'RAND()'
            ])
            ->first();
        if(empty($random_ultimate->id))
            return false;
		$user_ultimate = $this->newEntity(
			[
				'user_id' => $user_id,
				'ultimate_id' => $random_ultimate->id
			],
			['validate' => false]
		);
		$this->save($user_ultimate);
		return $random_ultimate;
	}
}