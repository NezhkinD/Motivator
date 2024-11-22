<?php

namespace App\Helper;

use App\Dto\DateDto;
use App\Entity\RuleEntity;
use App\Entity\TodoPageEntity;
use App\Enum\CategoryEnum;
use App\Enum\CategoryTypeEnum;
use DateTime;

class PageHelper
{
    protected const DIR_ALL_RULES = __DIR__ . "/../../config/allRules.json";
    protected const MD_DATE_TIME_FORMAT = 'Y-m-dTH:i:s';

    /**
     * @throws \JsonException
     */
    public function createTodoPageContent(DateTime $dateTime): string
    {
        $dayOfWeekNumber = (int)$dateTime->format("w");
        $rules = json_decode(file_get_contents(self::DIR_ALL_RULES), true, 512, JSON_THROW_ON_ERROR);
        $nowDate = new DateTime();

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

        $properties[] = self::buildLine(DateDto::PARAM_CREATED_AT, $nowDate->format(self::MD_DATE_TIME_FORMAT));
        $properties[] = self::buildLine(DateDto::PARAM_UPDATED_AT, $nowDate->format(self::MD_DATE_TIME_FORMAT));
        $properties[] = self::buildLine(CategoryEnum::total->value, 0);

        return self::createPropertiesMd($properties);
    }

    public function getPropertiesFromPage(array $pages): array
    {
        TodoPageEntity::fromData($pages, $this->getRules());
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