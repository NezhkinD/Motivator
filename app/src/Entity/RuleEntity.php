<?php

namespace App\Entity;

use App\Enum\CategoryEnum;
use App\Enum\CategoryTypeEnum;
use App\Enum\ConditionEnum;
use App\Enum\DaysEnum;

class RuleEntity
{
    protected const CATEGORY_NAME_FIELD = 'name';
    protected const CATEGORY_TYPE_FIELD = 'type';
    protected const WORKING_DAYS_FIELD = 'workingDays';
    protected const CONDITIONS_FIELD = 'conditions';
    protected const ENABLED = 'enabled';
    protected const RULE_COUNT = 'ruleCount';
    protected const COUNT_POINTS_SUCCESS = 'countPointsSuccess';
    protected const COUNT_POINTS_FAIL = 'countPointsFail';
    protected const MULTI_FACTOR = 'countPointsFail';

    public CategoryEnum $category;
    public CategoryTypeEnum $type;

    /** @var array<int> */
    public array $workingDays = [];

    /** @var ConditionEnum[] */
    public array $conditions = [];

    /** Правило включено */
    public bool $enabled = false;

    /** Норма выполнения */
    public int $ruleCount = 0;

    /** Очки за выполнение */
    public float $pointsSuccess = 0.0;

    /** Очки за НЕвыполнение */
    public float $pointsFail = 0.0;

    /**
     * Получаем результат по категории за предыдущий день,
     * умножаем на коэффициент и прибавляем к текущему результату
     */
    protected float $multiFactor = 0.0;

    public static function createFromArray(array $data): self
    {
        $entity = new self();
        $entity->category = CategoryEnum::fromString($data[self::CATEGORY_NAME_FIELD] ?? "");
        $entity->enabled = $data[self::ENABLED] ?? false;
        $entity->ruleCount = $data[self::RULE_COUNT] ?? 0;
        $entity->pointsSuccess = $data[self::COUNT_POINTS_SUCCESS] ?? 0;
        $entity->pointsFail = $data[self::COUNT_POINTS_FAIL] ?? 0;
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
        $entity->type = CategoryTypeEnum::fromString($data[self::CATEGORY_TYPE_FIELD] ?? "");

        foreach ($data[self::CONDITIONS_FIELD] ?? [] as $item) {
            $entity->conditions[] = ConditionEnum::fromString($item);
        }

        return $entity;
    }
}