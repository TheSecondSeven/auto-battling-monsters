<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;

class UserSkillsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('Timestamp');
        $this->belongsTo('Users');
        $this->belongsTo('Skills');
    }
    
    public function addSkillToUser($user_id, $skill_id) {
        $already_own = $this
            ->find()
            ->where([
                'UserSkills.skill_id' => $skill_id,
                'UserSkills.user_id' => $user_id
            ])
            ->first();
        if(!empty($already_own->id))
            return null;

		$user_skill = $this->newEntity(
			[
				'user_id' => $user_id,
				'skill_id' => $skill_id
			],
			['validate' => false]
		);
		$this->save($user_skill);
		return $user_skill->id;
	}
    public function addRandomSkillToUser($user_id, $rarity=null, $types = []) {
        $conditions = [];
        if(!empty($rarity))
            $conditions['Skills.rarity'] = $rarity;
        if(!empty($types))
            $conditions['Skills.type_id IN'] = $types;
        $random_skill = $this->Skills
            ->find()
            ->where($conditions)
            ->notMatching('UserSkills', function ($q) use ($user_id) {
                return $q->where(['UserSkills.user_id' => $user_id]);
            })
            ->order([
                'RAND()'
            ])
            ->first();
        if(empty($random_skill->id))
            return false;
		$user_skill = $this->newEntity(
			[
				'user_id' => $user_id,
				'skill_id' => $random_skill->id
			],
			['validate' => false]
		);
		$this->save($user_skill);
		return $random_skill;
	}
}