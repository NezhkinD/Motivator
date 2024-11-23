<?php

namespace App\Helper;

use App\Entity\RuleEntity;
use App\Entity\TodoPageEntity;
use App\Enum\CategoryTypeEnum;

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
        foreach ($todoPage->ruleEntities as $ruleEntity) {
            if (!$ruleEntity->enabled) {
                continue;
            }

            $taskEntityIndex = $todoPage->findByCategoryEnumInTasks($ruleEntity->category);
            if ($taskEntityIndex === null) {
                continue;
            }
            $score = $this->count($ruleEntity->type, $todoPage->taskEntities[$taskEntityIndex]->result, $ruleEntity);
            $todoPage->taskEntities[$taskEntityIndex]->score = $score;
            $total += $score;
        }

        $todoPage->total = $total;
        $todoPage->updatedAt = new \DateTime();
        return $todoPage;
    }

    protected function count(CategoryTypeEnum $category, int $currentResult, RuleEntity $ruleEntity): float
    {
        switch ($category) {
            case CategoryTypeEnum::boolean:
            case CategoryTypeEnum::number:
            case CategoryTypeEnum::ml:
                if ($currentResult > 0) {
                    $score = $ruleEntity->pointsSuccess;
                } else {
                    $score = $ruleEntity->pointsFail;
                }
                break;
            case CategoryTypeEnum::kcal:
                if ($currentResult <= $ruleEntity->ruleCount) {
                    $score = $ruleEntity->pointsSuccess;
                } else {
                    $score = $ruleEntity->pointsFail;
                }
                break;
            case CategoryTypeEnum::steps:
                if ($currentResult >= $ruleEntity->ruleCount) {
                    $score = ($currentResult - $ruleEntity->ruleCount) / $ruleEntity->ruleCount * $ruleEntity->pointsSuccess;
                } else {
                    $score = $ruleEntity->pointsFail;
                }
                break;
        }

        return round($score ?? 0.0, 1);
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