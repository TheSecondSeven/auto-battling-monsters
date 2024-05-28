<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Skill extends Entity
{
    protected function _getOwned()
    {
        return !empty($this->user_skills);
    }
    protected function _getMoveType()
    {
        return 'Skill';
    }
}