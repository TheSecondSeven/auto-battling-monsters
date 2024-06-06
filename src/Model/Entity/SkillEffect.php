<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

class SkillEffect extends Entity
{
    protected function _getEffectVerbose() {
        if(in_array($this->effect, ['Physical Damage','Magical Damage','True Damage'])) {
            return $this->get('amount_verbose').' '.$this->effect;
        }elseif(in_array($this->effect, ['Leech'])) {
            return $this->effect.' '.$this->get('amount_verbose').' Magical Damage';
        }elseif(in_array($this->effect, ['Heal'])) {
            return $this->effect.' for '.$this->get('amount_verbose');
        }elseif(in_array($this->effect, ['Heal Over Time'])) {
            return 'Heal for '.$this->get('amount_verbose').' for '.$this->duration.' second'.($this->duration > 1 ? 's' : '');
        }elseif(in_array($this->effect, ['Poison'])) {
            return $this->get('amount_verbose').' Poison Damage for '.$this->duration.' second'.($this->duration > 1 ? 's' : '');
        }elseif(in_array($this->effect, ['Attack Up', 'Attack Down', 'Defense Up', 'Defense Down', 'Speed Up', 'Speed Down', 'Evade Up', 'Evade Down'])) {
            return $this->effect.' '.$this->get('amount_verbose').' for '.$this->duration.' turn'.($this->duration > 1 ? 's' : '');
        }elseif(in_array($this->effect, ['Bubble','Confuse'])) {
            return $this->effect;
        }elseif(in_array($this->effect, ['Stun','Sleep','Freeze'])) {
            if($this->duration > 0) {
                return $this->effect.' for '.$this->duration.' second'.($this->duration > 1 ? 's' : '');
            }else{
                return 'Interrupt';
            }
        }elseif(in_array($this->effect, ['Burn'])) {
            return $this->effect.' for '.number_format(BURN_DURATION / 1000).' second'.(number_format(BURN_DURATION / 1000) > 1 ? 's' : '');
        }elseif(in_array($this->effect, ['Wet'])) {
            return $this->effect.' for '.number_format(WETNESS_DURATION / 1000).' second'.(number_format(WETNESS_DURATION / 1000) > 1 ? 's' : '');
        }elseif(in_array($this->effect, ['Infect'])) {
            return $this->get('amount_verbose').' Stack'.($this->get('amount_verbose') == 1 ? '' : 's').' of '.$this->effect;
        }elseif(in_array($this->effect, ['Cleanse'])) {
            return $this->effect.' '.$this->get('amount_verbose').' Debuff'.($this->amount_min > 1 ? 's' : '');
        }elseif(in_array($this->effect, ['Purge'])) {
            return $this->effect.' '.$this->get('amount_verbose').' Buff'.($this->amount_min > 1 ? 's' : '');
        }elseif(in_array($this->effect, ['Consume'])) {
            $status_effects = TableRegistry::getTableLocator()->get('Statuses')
                ->find()
                ->where([
                    'Statuses.type !=' => 'Status'
                ])
                ->all()
                ->toList();
            $status_effects_list = [];
            foreach($status_effects as $status_effect) {
                $status_effects_list[$status_effect->class] = $status_effect->name;
            }
            return $this->effect.' '.$this->get('amount_verbose').' Stack'.($this->get('amount_verbose') == 1 ? '' : 's').' of '.$status_effects_list[$this->status];
        }elseif(in_array($this->effect, ['Undying','Blind','Reflect'])) {
            return $this->effect.' for '.$this->duration.' turn'.($this->duration > 1 ? 's' : '');
        }elseif(in_array($this->effect, ['Random Amount'])) {
            //shouldnt be called on random amount
            return '';
        }
    }
    protected function _getAmountVerbose() {
        
        if($this->effect == 'Consume') {
            $amount =  'All';
        }elseif($this->amount_min == 0.00 && $this->amount_max == 0.00) {
            if($this->effect == 'Poison') {
                $amount = 'Refresh';
            }else{
                $amount = 'N/A';
            }
        }elseif($this->amount_min == 9999.00 && $this->amount_max == 9999.00) {
            if($this->effect == 'Purge') {
                $amount =  'All';
            }elseif($this->effect == 'Cleanse') {
                $amount =  'All';
            }elseif($this->effect == 'True Damage') {
                $amount =  'Lethal';
            }else{
                $amount =  'All';
            }
        }elseif($this->amount_min == $this->amount_max) {
            $amount =  number_format($this->amount_min);
        }else{
            $amount = number_format($this->amount_min).'-'.number_format($this->amount_max);
        }
        if(in_array($this->effect, ['Attack Up', 'Attack Down', 'Defense Up', 'Defense Down', 'Speed Up', 'Speed Down', 'Evade Up', 'Evade Down',])) $amount .= '%';
        return $amount;
    }
}