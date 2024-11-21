<?php

namespace App\Helper;

use App\Entity\RuleEntity;
use App\Enum\CategoryEnum;

class PageCreator
{
    protected const DIR_ALL_RULES = __DIR__ . "/../../config/allRules.json";

    /**
     * @throws \JsonException
     */
    public function createTodoPageContent(\DateTimeInterface $dateTime): string
    {
        $dayOfWeekNumber = (int)$dateTime->format("w");
        $rules = json_decode(file_get_contents(self::DIR_ALL_RULES), true, 512, JSON_THROW_ON_ERROR);

        $tasks[] = "---";
        foreach ($rules as $rule) {
            $ruleEntity = RuleEntity::createFromArray($rule);
            if (!in_array($dayOfWeekNumber, $ruleEntity->workingDays)) {
                continue;
            }
            $name = null;
            if ($ruleEntity->category === CategoryEnum::water || $ruleEntity->category === CategoryEnum::calories) {
                $name = $ruleEntity->category->value . " " . $ruleEntity->ruleCount . " " . $ruleEntity->type->name . " :";
            }

            $tasks[] = $name ?? ($ruleEntity->category->value . ":");
        }
        $tasks[] = "---";
        return implode("\n", $tasks);
    }
}