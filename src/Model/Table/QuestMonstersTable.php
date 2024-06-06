<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\Query\SelectQuery;

class QuestMonstersTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('Timestamp');
		$this->belongsTo('Types');
		$this->belongsTo('Quests');
        $this->belongsTo('Skill1')
			->setForeignKey('skill_1_id')
			->setClassName('Skills');
		$this->belongsTo('Skill2')
			->setForeignKey('skill_2_id')
			->setClassName('Skills');
		$this->belongsTo('Skill3')
			->setForeignKey('skill_3_id')
			->setClassName('Skills');
		$this->belongsTo('Skill4')
			->setForeignKey('skill_4_id')
			->setClassName('Skills');
		$this->belongsTo('Ultimates');
    }
	public function findForBattle(SelectQuery $query)
    {
        return $query->contain([
            'Types',
            'Skill1' => [
                'SkillEffects' => [
                    'SecondarySkillEffects',
                ],
                'Types'
            ],
            'Skill2' => [
                'SkillEffects' => [
                    'SecondarySkillEffects'
                ],
                'Types'
            ],
            'Skill3' => [
                'SkillEffects' => [
                    'SecondarySkillEffects'
                ],
                'Types'
            ],
            'Skill4' => [
                'SkillEffects' => [
                    'SecondarySkillEffects'
                ],
                'Types'
            ],
            'Ultimates' => [
                'SkillEffects' => [
                    'SecondarySkillEffects'
                ],
                'Types'
            ]
        ]);
    }
}