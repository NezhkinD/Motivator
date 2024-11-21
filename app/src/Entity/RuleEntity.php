<?php

namespace App\Entity;

use App\Enum\CategoryEnum;
use App\Enum\CategoryTypeEnum;
use App\Enum\ConditionEnum;
use App\Enum\DaysEnum;

class RuleEntity
{
    protected const CATEGORY_FIELD = 'category';
    protected const TYPE_FIELD = 'type';
    protected const WORKING_DAYS_FIELD = 'workingDays';
    protected const CONDITIONS_FIELD = 'conditions';
    protected const ENABLED = 'enabled';
    protected const RULE_COUNT = 'ruleCount';
    protected const COUNT_POINTS_SUCCESS = 'countPointsSuccess';
    protected const COUNT_POINTS_FAIL = 'countPointsFail';
    protected const MULTI_FACTOR = 'countPointsFail';

    protected CategoryEnum $category;
    protected CategoryTypeEnum $type;

    /** @var array<int> */
    protected array $workingDays = [];

    /** @var ConditionEnum[] */
    protected array $conditions = [];

    protected bool $enabled = false;
    protected int $ruleCount = 0;
    protected int $countPointsSuccess = 0;
    protected int $countPointsFail = 0;

    /** Коэффицент увеличения  */
    protected float $multiFactor = 0.0;

    public static function createFromArray(array $data): self
    {
        $entity = new self();
        $entity->category = CategoryEnum::fromString($data[self::CATEGORY_FIELD] ?? "");
        $entity->enabled = $data[self::ENABLED] ?? false;
        $entity->ruleCount = $data[self::RULE_COUNT] ?? 0;
        $entity->countPointsSuccess = $data[self::COUNT_POINTS_SUCCESS] ?? 0;
        $entity->countPointsFail = $data[self::COUNT_POINTS_FAIL] ?? 0;
        $entity->multiFactor = $data[self::MULTI_FACTOR] ?? 0.0;

        foreach ($data[self::WORKING_DAYS_FIELD] ?? [] as $day) {
            $daysEnum = DaysEnum::fromString($day);
            switch ($daysEnum) {
                case DaysEnum::all:
                    array_push($entity->workingDays, 1, 2, 3, 4, 5, 6, 7);
                    break;
                case DaysEnum::weekdayOnly:
                    array_push($entity->workingDays, 1, 2, 3, 4, 5);
                    break;
                case DaysEnum::weekendsOnly:
                    array_push($entity->workingDays, 6, 7);
                    break;
                default:
                    $entity->workingDays[] = (int)$day;
            }
        }
        $entity->workingDays = array_unique($entity->workingDays, SORT_NUMERIC);

        foreach ($data[self::CONDITIONS_FIELD] ?? [] as $item) {
            $entity->conditions[] = ConditionEnum::fromString($item);
        }

        return $entity;
    }
}