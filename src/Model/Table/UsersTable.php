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
}