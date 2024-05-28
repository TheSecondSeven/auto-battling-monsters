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
}