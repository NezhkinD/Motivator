<?php

namespace App\Helper;

use App\Entity\TodoPageEntity;
use App\Enum\CategoryTypeEnum;
use DateTime;

class ScoreCounter
{
    /**
     * @param TodoPageEntity $todoPage
     * @param TodoPageEntity[] $todoPagesEntities
     * @return TodoPageEntity
     */
    public function countTotal(TodoPageEntity $todoPage, array $todoPagesEntities): TodoPageEntity
    {
        $total = 0.0;
        $pages = $this->buildPagesMap($todoPagesEntities);
        foreach ($todoPage->ruleEntities as $ruleEntity) {
            if (!$ruleEntity->enabled) {
                continue;
            }

            $taskEntityIndex = $todoPage->findByCategoryEnumInTasks($ruleEntity->category);
            if ($taskEntityIndex === null) {
                continue;
            }
            $selectTask = $todoPage->taskEntities[$taskEntityIndex];

            switch ($ruleEntity->type) {
                case CategoryTypeEnum::boolean:
                case CategoryTypeEnum::kcal:
                case CategoryTypeEnum::ml:
                    if ($selectTask->result === $ruleEntity->ruleCount) {
                        $selectTask->score = $ruleEntity->pointsSuccess;
                    } else {
                        $selectTask->score = $ruleEntity->pointsFail;
                    }
                    break;
                default:
                    $selectTask->score = 0;
            }

            $todoPage->taskEntities[$taskEntityIndex] = $selectTask;
        }

        return $todoPage;
    }

    /**
     * @param TodoPageEntity[] $todoPagesEntities
     * @return array
     */
    protected function buildPagesMap(array $todoPagesEntities): array
    {
        $result = [];
        foreach ($todoPagesEntities as $todoPageEntity) {
            $result[$todoPageEntity->createdAt->format('Y-m-d')] = $todoPageEntity;
        }
        return $result;
    }
}