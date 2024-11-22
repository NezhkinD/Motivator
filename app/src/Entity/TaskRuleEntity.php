<?php

namespace App\Entity;

use App\Dto\DateDto;
use App\Dto\LineDto;
use App\Enum\CategoryEnum;

class TaskRuleEntity
{
    /** @var array<string, TaskEntity[]> */
    public array $taskEntities;

    /** @var RuleEntity[] */
    public array $ruleEntities;

    /**
     * @param RuleEntity[] $rules
     * @param array[] $pages
     * @return self
     * @throws \Exception
     */
    public static function fromData(array $rules, array $pages): self
    {
        $self = new self();
        $self->ruleEntities = $rules;

        foreach ($pages as $page) {
            $page = array_filter($page, function (string $value) {
                return strlen(trim($value)) > 3;
            });

            $page = array_map(function (string $value) {
                return trim($value);
            }, $page);

            $createdAtStr = trim(array_find($page, function (string $value) {
                return strripos($value, TaskEntity::FIELD_CREATED_AT) !== false;
            }));

            $updatedAtStr = trim(array_find($page, function (string $value) {
                return strripos($value, TaskEntity::FIELD_UPDATED_AT) !== false;
            }));

            if (strlen($updatedAtStr) > 20 && strlen($createdAtStr) > 20) {
                $createdAt = DateDto::fromStr($createdAtStr)->value;
                $updatedAt = DateDto::fromStr($updatedAtStr)->value;
            } else {
                $createdAt = new \DateTime();
                $updatedAt = new \DateTime();
                // TODO добавить в лог - Не удалось получить дату создания и/или дату обновления
            }


            foreach ($page as $line) {
                if ($line === $createdAtStr || $line === $updatedAtStr) {
                    continue;
                }

                $lineDto = LineDto::fromStr($line);
                if (!CategoryEnum::has($lineDto->name)) {
                    // TODO добавить в лог - Не удалось определить категорию
                    continue;
                }

                $self->taskEntities[] = TaskEntity::create(CategoryEnum::fromString($lineDto->name), $lineDto->value, $createdAt, $updatedAt);
            }
        }

        return $self;
    }
}