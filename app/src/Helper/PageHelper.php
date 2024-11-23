<?php

namespace App\Helper;

use AllowDynamicProperties;
use App\Dto\DateDto;
use App\Entity\RuleEntity;
use App\Entity\TodoPageEntity;
use App\Enum\CategoryEnum;
use App\Enum\CategoryTypeEnum;
use DateTime;

#[AllowDynamicProperties]
class PageHelper
{
    protected const string MD_DATE_TIME_FORMAT = 'Y-m-dTH:i:s';
    protected const string DIR_ALL_RULES = __DIR__ . "/../../config/allRules.json";

    public function __construct(ScoreCounter $scoreCounter)
    {
        $this->scoreCounter = $scoreCounter;
    }

    /**
     * @throws \JsonException
     * todo добавить в параметры правила
     */
    public function createNewTodoPageContent(DateTime $dateTime, array $rules): string
    {
        $dayOfWeekNumber = (int)$dateTime->format("w");
        foreach ($rules as $rule) {
            $ruleEntity = RuleEntity::createFromArray($rule);
            if (!in_array($dayOfWeekNumber, $ruleEntity->workingDays)) {
                continue;
            }

            if ($ruleEntity->category === CategoryEnum::water || $ruleEntity->category === CategoryEnum::calories) {
                $name = $ruleEntity->category->value . " " . $ruleEntity->ruleCount . " " . $ruleEntity->type->name;
            }

            $value = match ($ruleEntity->type) {
                CategoryTypeEnum::boolean, CategoryTypeEnum::kcal, CategoryTypeEnum::ml => "false",
                default => 0,
            };

            $properties[] = ($name ?? $ruleEntity->category->value) . ": $value";
            $name = null;
        }

        return self::createPropertiesMd(array_merge($properties ?? [], self::buildInfo($dateTime, $dateTime, 0.0)));
    }

    public function countScoresAndReturnTodoPageContent(TodoPageEntity $entity): string
    {
        $updatePageEntity = $this->scoreCounter->countTotal($entity, []);

        foreach ($updatePageEntity->taskEntities as $taskEntity) {
            $properties[] = self::buildLine($taskEntity->category->value, $taskEntity->score);
        }

        return self::createPropertiesMd(array_merge($properties ?? [], self::buildInfo($entity->createdAt, $entity->updatedAt, $entity->total)));
    }

    public function getPropertiesFromPage(array $pages): array
    {
        TodoPageEntity::fromData($pages, $this->getRules());
    }

    protected static function buildInfo(DateTime $createdAt, DateTime $updatedAt, float $total): array
    {
        $properties[] = self::buildLine(DateDto::PARAM_CREATED_AT, $createdAt->format(self::MD_DATE_TIME_FORMAT));
        $properties[] = self::buildLine(DateDto::PARAM_UPDATED_AT, $updatedAt->format(self::MD_DATE_TIME_FORMAT));
        $properties[] = self::buildLine(CategoryEnum::total->value, $total);

        return $properties;
    }

    /**
     * @return RuleEntity[]
     * @throws \JsonException
     */
    protected function getRules(?int $selectDayNumber = null): array
    {
        $rules = json_decode(file_get_contents(self::DIR_ALL_RULES), true, 512, JSON_THROW_ON_ERROR);
        $ruleEntities = [];
        foreach ($rules as $rule) {
            $ruleEntity = RuleEntity::createFromArray($rule);

            if ($selectDayNumber !== null && !in_array($selectDayNumber, $ruleEntity->workingDays)) {
                continue;
            }

            $ruleEntities[] = $ruleEntity;
        }
        return $ruleEntities;
    }

    protected static function buildLine(string $name, $value): string
    {
        return $name . ": " . $value;
    }

    protected static function createPropertiesMd(array $properties): string
    {
        return "---\n" . implode("\n", $properties) . "\n---\n";
    }
}