<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class QuestMonster extends Entity
{
    protected function _getListOfAbilities()
    {
        $abilities = '';
        if(!empty($this->skill1->id)) $abilities .= $this->skill1->name;
        if(!empty($this->skill2->id)) $abilities .= ' | '.$this->skill2->name;
        if(!empty($this->skill3->id)) $abilities .= ' | '.$this->skill3->name;
        if(!empty($this->skill4->id)) $abilities .= ' | '.$this->skill4->name;
        if(!empty($this->ultimate->id)) $abilities .= ' | '.$this->ultimate->name;
        return $abilities;
    }
}