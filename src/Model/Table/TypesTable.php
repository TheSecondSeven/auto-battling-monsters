<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;

class TypesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('Timestamp');
		$this->hasMany('Monsters');
    }
}