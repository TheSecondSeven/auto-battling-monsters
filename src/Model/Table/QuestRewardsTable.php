<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\TableRegistry;

class QuestRewardsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('Timestamp');
		$this->belongsTo('Quests');
		$this->belongsTo('Skills');
		$this->belongsTo('Ultimates');
		$this->belongsTo('Types');
		$this->belongsTo('SecondaryTypes')
			->setForeignKey('secondary_type_id')
			->setClassName('Types');
    }

    public static function reward_types() {
		$rewards =  [
			'Skill',
            'Ultimate',
            'Rune',
            'Monster',
            'Dual Type Monster',
            'Gems',
            'Gold',
            'Rune Shards'
		];
		$reward_type_options = array_combine($rewards, $rewards);
		return $reward_type_options;
	}
    public function grantRewardToUser($quest_reward, $user) {
        $user_quest_reward = TableRegistry::getTableLocator()->get('UserQuestRewards')->newEntity([
            'quest_id' => $quest_reward->quest_id,
            'user_id' => $user->id,
            'reward_type' => $quest_reward->reward_type,
            'skill_id' => $quest_reward->skill_id,
            'ultimate_id' => $quest_reward->ultimate_id,
            'type_id' => $quest_reward->type_id,
            'secondary_type_id' => $quest_reward->secondary_type_id,
            'amount' => $quest_reward->amount
        ]);
        if($quest_reward->reward_type == 'Skill') {
            if(!empty($quest_reward->skill->id)) {
                $result = TableRegistry::getTableLocator()->get('UserSkills')->addSkillToUser($user->id, $quest_reward->skill->id);
                if(empty($result)) {
                    $random_skill = TableRegistry::getTableLocator()->get('UserSkills')->addRandomSkillToUser($user->id, $quest_reward->rarity);
                    if(empty($random_skill->id)) {
                        //change reward to gold
                        $amount = TableRegistry::getTableLocator()->get('Users')->giveGoldToUserByRarity($user->id, $quest_reward->rarity);
                        $user_quest_reward->reward_type = 'Gold';
                        $user_quest_reward->amount = $amount;
                    }else{
                        $user_quest_reward->skill_id = $random_skill->id;
                    }
                }
            }elseif(!empty($quest_reward->type->id)) {
                $random_skill = TableRegistry::getTableLocator()->get('UserSkills')->addRandomSkillToUser($user->id, $quest_reward->rarity, [$quest_reward->type->id]);
                if(empty($random_skill->id)) {
                    $random_skill = TableRegistry::getTableLocator()->get('UserSkills')->addRandomSkillToUser($user->id, $quest_reward->rarity);
                    if(empty($random_skill->id)) {
                        //change reward to gold
                        $amount = TableRegistry::getTableLocator()->get('Users')->giveGoldToUserByRarity($user->id, $quest_reward->rarity);
                        $user_quest_reward->reward_type = 'Gold';
                        $user_quest_reward->amount = $amount;
                    }else{
                        $user_quest_reward->skill_id = $random_skill->id;
                    }
                }else{
                    $user_quest_reward->skill_id = $random_skill->id;
                }
            }elseif($quest_reward->usable) {
                $random_skill = TableRegistry::getTableLocator()->get('UserSkills')->addRandomSkillToUser($user->id, $quest_reward->rarity, $user->usable_types);
                if(empty($random_skill->id)) {
                    $random_skill = TableRegistry::getTableLocator()->get('UserSkills')->addRandomSkillToUser($user->id, $quest_reward->rarity);
                    if(empty($random_skill->id)) {
                        //change reward to gold
                        $amount = TableRegistry::getTableLocator()->get('Users')->giveGoldToUserByRarity($user->id, $quest_reward->rarity);
                        $user_quest_reward->reward_type = 'Gold';
                        $user_quest_reward->amount = $amount;
                    }else{
                        $user_quest_reward->skill_id = $random_skill->id;
                    }
                }else{
                    $user_quest_reward->skill_id = $random_skill->id;
                }
            }else{
                $random_skill = TableRegistry::getTableLocator()->get('UserSkills')->addRandomSkillToUser($user->id, $quest_reward->rarity);
                if(empty($random_skill->id)) {
                    //change reward to gold
                    $amount = TableRegistry::getTableLocator()->get('Users')->giveGoldToUserByRarity($user->id, $quest_reward->rarity);
                    $user_quest_reward->reward_type = 'Gold';
                    $user_quest_reward->amount = $amount;
                }else{
                    $user_quest_reward->skill_id = $random_skill->id;
                }
            }
        }elseif($quest_reward->reward_type == 'Rune') {
            if(!empty($quest_reward->type->id)) {
                $rune = TableRegistry::getTableLocator()->get('Runes')->addRuneToUser($user->id, [$quest_reward->type->id]);
            }elseif($quest_reward->usable) {
                $rune = TableRegistry::getTableLocator()->get('Runes')->addRuneToUser($user->id, $user->usable_types);
            }else{
                $rune = TableRegistry::getTableLocator()->get('Runes')->addRuneToUser($user->id);
            }
            $user_quest_reward->type_id = $rune->type_id;
        }elseif($quest_reward->reward_type == 'Rune Shards') {
            TableRegistry::getTableLocator()->get('Users')->giveRuneShardsToUser($user->id, $quest_reward->amount);
        }elseif($quest_reward->reward_type == 'Gold') {
            TableRegistry::getTableLocator()->get('Users')->giveGoldToUser($user->id, $quest_reward->amount);
        }elseif($quest_reward->reward_type == 'Gems') {
            TableRegistry::getTableLocator()->get('Users')->giveGemsToUser($user->id, $quest_reward->amount);
        }
        // }elseif($quest_reward->reward_type == 'Ultimate') {
        //     if(!empty($quest_reward->ultimate->id)) {
        //         return 'Ultimate Unlock: '.$quest_reward->ultimate->name;
        //     }elseif(!empty($quest_reward->type->id)) {
        //         return 'Random '.$quest_reward->type->name.' Ultimate';
        //     }elseif($quest_reward->usable) {
        //         return 'Random '.$quest_reward->rarity.' Ultimate';
        //     }else{
        //         return 'Random '.$quest_reward->rarity.' Ultimate';
        //     }
        // }elseif($quest_reward->reward_type == 'Rune') {
        //     if(!empty($quest_reward->type->id)) {
        //         return $quest_reward->type->name.' Rune';
        //     }elseif($quest_reward->usable) {
        //         return 'Random Rune';
        //     }else{
        //         return 'Random Rune';
        //     }
        // }elseif($quest_reward->reward_type == 'Monster') {
        //     if(!empty($quest_reward->type->id)) {
        //         return $quest_reward->type->name.' Monster';
        //     }else{
        //         return 'Random Single Type Monster';
        //     }
        // }elseif($quest_reward->reward_type == 'Monster') {
        //     if(!empty($quest_reward->type->id) && !empty($quest_reward->secondary_type->id)) {
        //         return $quest_reward->type->name.'/'.$quest_reward->secondary_type->name.' Monster';
        //     }else{
        //         return 'Random Dual Type Monster';
        //     }
        // }elseif($quest_reward->reward_type == 'Gems') {
        //     return $quest_reward->amount.' Gem'.($quest_reward->amount == 1 ? '' : 's').'!';
        // }elseif($quest_reward->reward_type == 'Gold') {
        //     return $quest_reward->amount.' Gold';
        // }elseif($quest_reward->reward_type == 'Rune Shards') {
        //     return $quest_reward->amount.' Rune Shards';
        // }
        TableRegistry::getTableLocator()->get('UserQuestRewards')->save($user_quest_reward);
		return $user_quest_reward->id;
	}
}