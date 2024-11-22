<?php

namespace App\Entity;

use App\Dto\DateDto;
use App\Dto\LineDto;
use App\Enum\CategoryEnum;
use DateTime;

class TodoPageEntity
{
    public const FIELD_CREATED_AT = 'created_at';
    public const FIELD_UPDATED_AT = 'updated_at';
    public const FIELD_TOTAL = 'total';

    /** @var TaskEntity[] */
    public array $taskEntities;

    /** @var RuleEntity[] */
    public array $ruleEntities;

    public DateTime $createdAt;
    public DateTime $updatedAt;
    public int $total;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
        $this->total = 0;
    }

    /**
     * @param array $rules
     * @param array[] $tasks
     * @return self
     * @throws \Exception
     */
    public static function fromData(array $rules, array $tasks): self
    {
        $self = new self();
        foreach ($rules as $rule) {
            $self->ruleEntities[] = RuleEntity::createFromArray($rule);
        }

        foreach ($tasks as $page) {
            $page = $self->mapTrim($self->filter($page));
            $createdAtStr = $self->find($page, self::FIELD_CREATED_AT);
            $updatedAtStr = $self->find($page, self::FIELD_UPDATED_AT);
            $totalStr = $self->find($page, self::FIELD_TOTAL);

            if (strlen($updatedAtStr) > 20 && strlen($createdAtStr) > 20 && strlen($totalStr) > 7) {
                $self->createdAt = DateDto::fromStr($createdAtStr)->value;
                $self->updatedAt = DateDto::fromStr($updatedAtStr)->value;
                $self->total = LineDto::fromStr($totalStr)->value;
            } else {
                // TODO добавить в лог - Не удалось получить дату создания и/или дату обновления
            }

            foreach ($page as $line) {
                if ($line === $createdAtStr || $line === $updatedAtStr || $line === $totalStr) {
                    continue;
                }

                $lineDto = LineDto::fromStr($line);
                if (!CategoryEnum::has($lineDto->name)) {
                    // TODO добавить в лог - Не удалось определить категорию
                    continue;
                }

                $self->taskEntities[] = TaskEntity::create(CategoryEnum::fromString($lineDto->name), $lineDto->value, 0);
            }
        }

        return $self;
    }

    public function updateTask(TaskEntity $taskEntity): self
    {
        foreach ($this->taskEntities as $key => $oldTaskEntity) {
            if ($oldTaskEntity->category === $taskEntity->category) {
                $this->updatedAt = new DateTime();
                $this->taskEntities[$key] = $taskEntity;
            }
        }
        return $this;
    }

    public function findByCategoryEnumInTasks(CategoryEnum $category): ?int
    {
        $found = array_find_key($this->taskEntities, function (TaskEntity $value) use ($category) {
           return $value->category === $category;
        });

        return $found ?? null;
    }

    protected function find(array $page, string $needle): string
    {
        return trim(array_find($page, function (string $value) use ($needle) {
            return strripos($value, $needle) !== false;
        }));
    }

    protected function filter(array $page, int $minLen = 3): array
    {
        return array_filter($page, function (string $value) use ($minLen) {
            return strlen(trim($value)) > $minLen;
        });
    }

    protected function mapTrim(array $page): array
    {
        return array_map(function (string $value) {
            return trim($value);
        }, $page);
    }
}