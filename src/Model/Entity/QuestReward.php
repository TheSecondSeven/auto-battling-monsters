<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class QuestReward extends Entity
{
    protected function _getReward()
    {
        if($this->mystery) return '???';
        return $this->get('real_reward');
    }
    protected function _getRealReward()
    {
        if($this->reward_type == 'Skill') {
            if(!empty($this->skill->id)) {
                return 'Skill Unlock: '.$this->skill->name;
            }elseif(!empty($this->type->id)) {
                return 'Random '.$this->type->name.' Skill';
            }elseif($this->usable) {
                return 'Random '.$this->rarity.' Skill';
            }else{
                return 'Random '.$this->rarity.' Skill';
            }
        }elseif($this->reward_type == 'Ultimate') {
            if(!empty($this->ultimate->id)) {
                return 'Ultimate Unlock: '.$this->ultimate->name;
            }elseif(!empty($this->type->id)) {
                return 'Random '.$this->type->name.' Ultimate';
            }elseif($this->usable) {
                return 'Random '.$this->rarity.' Ultimate';
            }else{
                return 'Random '.$this->rarity.' Ultimate';
            }
        }elseif($this->reward_type == 'Rune') {
            if(!empty($this->type->id)) {
                return 'Level '.$this->amount.' '.$this->type->name.' Rune';
            }elseif($this->usable) {
                return 'Random Level '.$this->amount.' Rune';
            }else{
                return 'Random Level '.$this->amount.' Rune';
            }
        }elseif($this->reward_type == 'Monster') {
            if(!empty($this->type->id)) {
                return $this->type->name.' Monster';
            }else{
                return 'Random Single Type Monster';
            }
        }elseif($this->reward_type == 'Dual Type Monster') {
            if(!empty($this->type->id) && !empty($this->secondary_type->id)) {
                return $this->type->name.'/'.$this->secondary_type->name.' Monster';
            }else{
                return 'Random Dual Type Monster';
            }
        }elseif($this->reward_type == 'Gems') {
            return $this->amount.' Gem'.($this->amount == 1 ? '' : 's').'!';
        }elseif($this->reward_type == 'Gold') {
            return $this->amount.' Gold';
        }elseif($this->reward_type == 'Rune Shards') {
            return $this->amount.' Rune Shards';
        }
        return 'No Reward';
    }
}
