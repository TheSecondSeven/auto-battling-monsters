<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Monster extends Entity
{
    protected function _getListOfAbilities()
    {
        $abilities = '';
        if(!empty($this->skill1->id)) $abilities .= $this->skill1->name;
        if(!empty($this->skill2->id)) $abilities .= ', '.$this->skill2->name;
        if(!empty($this->skill3->id)) $abilities .= ', '.$this->skill3->name;
        if(!empty($this->skill4->id)) $abilities .= ', '.$this->skill4->name;
        if(!empty($this->ultimate->id)) $abilities .= ', and '.$this->ultimate->name;
        return $abilities;
    }
    protected function _getMoveset()
    {
        $abilities = '';
        if(!empty($this->skill1->id)) $abilities .= 'Skill 1: '.$this->skill1->name;
        if(!empty($this->skill2->id)) $abilities .= ' | Skill 2: '.$this->skill2->name;
        if(!empty($this->skill3->id)) $abilities .= ' | Skill 3: '.$this->skill3->name;
        if(!empty($this->skill4->id)) $abilities .= ' | Skill 4: '.$this->skill4->name;
        if(!empty($this->ultimate->id)) $abilities .= ' | Ultimate: '.$this->ultimate->name;
        if(empty($abilities)) $abilities = 'No Moves Set';
        return $abilities;
    }
    protected function _getTypeVerbose() {
        if(empty($this->type->name))
            return 'No Type Data';
        $types = $this->type->name;
        if(!empty($this->secondary_type->id))
            $types .= '/'.$this->secondary_type->name;
        return $types;
    }
}