<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Quest extends Entity
{

    protected function _getRewards()
    {
        if(!empty($this->quest_rewards)) {
            $rewards = '';
            foreach($this->quest_rewards  as $index=>$reward) {
                if($index > 0) $rewards .= '<br>';
                $rewards .= $reward->get('reward');
            }
            return $rewards;
        }
        return 'No Rewards';
    }
    protected function _getUserRewards()
    {
        if(!empty($this->user_quest_rewards)) {
            $rewards = '';
            foreach($this->user_quest_rewards  as $index=>$reward) {
                if($index > 0) $rewards .= '<br>';
                $rewards .= $reward->get('reward');
            }
            return $rewards;
        }
        return 'No Rewards';
    }
    protected function _getMonsters()
    {
        if(!empty($this->quest_monsters)) {
            $monsters = '';
            foreach($this->quest_monsters  as $index=>$monster) {
                if($index > 0) $monsters .= '<br>';
                $monsters .= $monster->name;
            }
            return $monsters;
        }
        return 'No Monsters';
    }

    protected function _getRequiredRestVerbose() {
        $return = '';
        if(empty($this->required_rest)) {
            $return = 'None';
        }else{
            $days = floor($this->required_rest / (60 * 24));
            $hours = floor(($this->required_rest % (60 * 24)) / 60);
            $minutes = floor($this->required_rest % 60);
            if($days > 0) {
                $return .= $days.' day'.($days == 1 ? '' : 's');
            }elseif($hours > 0) {
                $return .= ' '.$hours.' hour'.($hours == 1 ? '' : 's');
            }elseif($minutes > 0) {
                $return .= ' '.$minutes.' minute'.($hours == 1 ? '' : 's');
            }
        }
        return $return;
    }
}