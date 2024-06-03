<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\Query\SelectQuery;
use Cake\Event\EventInterface;
use Cake\Datasource\EntityInterface;
use App\Model\Entity\Quest;
use ArrayObject;

class QuestsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('Timestamp');
		$this->hasMany('QuestMonsters');
		$this->hasMany('QuestRewards');
		$this->hasMany('UserQuestRewards');
		$this->belongsToMany('Users');
		$this->belongsTo('ParentQuests')
            ->setForeignKey('quest_id')
            ->setClassName('Quests');
		$this->hasMany('ChildQuests')
            ->setForeignKey('quest_id')
            ->setClassName('Quests');
    }

    public function beforeSave(EventInterface $event, Quest $entity, ArrayObject $options)
    {
        $depth = 0;
        if(!empty($entity->quest_id)) {
            $quest_id = $entity->quest_id;
            while($quest_id != null) {
                $depth += 1;
                $depth_quest = $this
                    ->find()
                    ->where([
                        'Quests.id' => $quest_id
                    ])
                    ->first();
                if(!empty($depth_quest->quest_id)) {
                    $quest_id = $depth_quest->quest_id;
                }else{
                    $quest_id = null;
                }
            }
        }
        $entity->depth = $depth;
    }
}