<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\RulesChecker;
use Cake\ORM\Rule\IsUnique;
use Cake\Validation\Validator;

class UsersTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('Timestamp');
		$this->hasMany('UserSkills');
		$this->hasMany('UserQuestRewards');
		$this->hasMany('Monsters');
    }

    // Add the following method.
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->notEmptyString('username')
            ->notEmptyString('email');
        $validator->add('email', 'valid-email', ['rule' => 'email','message' => 'Please provide a valid email address.']);
        $validator->add('confirm_password', 'no-misspelling', [
            'rule' => ['compareWith', 'password'],
            'message' => 'Passwords are not equal',
        ]);

        return $validator;
    }
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->addCreate(new IsUnique(['username']), 'uniqueUsername', [
            'errorField' => 'username',
            'message' => 'Username taken.'
        ]);
        $rules->addCreate(new IsUnique(['email']), 'uniqueEmail', [
            'errorField' => 'email',
            'message' => 'An account already exists tied to this email. Please login.'
        ]);
        return $rules;
    }

    public function increaseMonsterLimit($user_id) {
        $user = $this->get($user_id);
        $user->active_monster_limit++;
        $this->save($user);
        return $amount;
    }

    public function removeGoldFromUser($user_id, $amount, $reason = null) {
        $user = $this->get($user_id);
        $user->gold = max(0, $user->gold - $amount);
        $this->save($user);
        return $amount;
    }

    public function removeRuneShardsFromUser($user_id, $amount, $reason = null) {
        $user = $this->get($user_id);
        $user->rune_shards = max(0, $user->rune_shards - $amount);
        $this->save($user);
        return $amount;
    }

    public function giveGoldToUserByRarity($user_id, $rarity, $reason = null) {
        $amount = 5;
        if($rarity == 'Legendary') {
			$amount = 500;
		}elseif($rarity == 'Epic') {
			$amount = 100;
        }elseif($rarity == 'Rare') {
			$amount = 20;
        }elseif($rarity == 'Uncommon') {
			$amount = 5;
        }elseif($rarity == 'Common') {
			$amount = 5;
        }
        $user = $this->get($user_id);
        $user->gold += $amount;
        $this->save($user);
        return $amount;
    }

    public function giveGoldToUser($user_id, $amount, $reason = null) {
        $user = $this->get($user_id);
        $user->gold += $amount;
        $this->save($user);
        return $amount;
    }

    public function giveRuneShardsToUser($user_id, $amount, $reason = null) {
        $user = $this->get($user_id);
        $user->rune_shards += $amount;
        $this->save($user);
        return $amount;
    }

    public function giveGemsToUser($user_id, $amount, $reason = null) {
        $user = $this->get($user_id);
        $user->gems += $amount;
        $this->save($user);
        return $amount;
    }
}