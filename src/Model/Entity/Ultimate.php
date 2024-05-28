<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Ultimate extends Entity
{
    protected function _getOwned()
    {
        return !empty($this->user_ultimates);
    }
    protected function _getMoveType()
    {
        return 'Ultimate';
    }
}