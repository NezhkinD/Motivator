<?php

namespace App\Entity;

use App\Dto\DateDto;
use App\Dto\LineDto;
use App\Enum\CategoryEnum;
use App\Helper\PageContentHelper;
use DateTime;

class TodoPageEntity
{
    public const string FIELD_CREATED_AT = 'created_at';
    public const string FIELD_UPDATED_AT = 'updated_at';
    public const string FIELD_TOTAL = 'total';
    protected const string MD_DATE_TIME_FORMAT = 'Y-m-dTH:i:s';

    /** @var TaskEntity[] */
    public array $taskEntities = [];

    /** @var RuleEntity[] */
    public array $ruleEntities;

    public DateTime $createdAt;
    public DateTime $updatedAt;
    public float $total;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
        $this->total = 0.0;
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

        $tasks = $self->mapTrim($self->filter($tasks));
        $createdAtStr = $self->find($tasks, self::FIELD_CREATED_AT);
        $updatedAtStr = $self->find($tasks, self::FIELD_UPDATED_AT);
        $totalStr = $self->find($tasks, self::FIELD_TOTAL);

        if (strlen($updatedAtStr) > 20 && strlen($createdAtStr) > 20 && strlen($totalStr) > 7) {
            $self->createdAt = DateDto::fromStr($createdAtStr)->value;
            $self->updatedAt = DateDto::fromStr($updatedAtStr)->value;
            $self->total = LineDto::fromStr($totalStr)->value;
        } else {
            // TODO добавить в лог - Не удалось получить дату создания и/или дату обновления
        }

        foreach ($tasks as $key => $line) {
            if ($line === $createdAtStr || $line === $updatedAtStr || $line === $totalStr) {
                continue;
            }

            $lineDto = LineDto::fromStr($line);
            if (!CategoryEnum::has($lineDto->name)) {
                // TODO добавить в лог - Не удалось определить категорию
                continue;
            }
            $self->taskEntities[$key] = TaskEntity::create(CategoryEnum::fromString($lineDto->name), $lineDto->value, 0);
        }


        return $self;
    }

    public function buildScoreInfo(): string
    {
        $date = getdate($this->createdAt->getTimestamp());

        $tasks = [];
        foreach ($this->taskEntities as $taskEntity) {
            $tasks[] = [$taskEntity->category->value, $taskEntity->score];
        }

        $tasks[] = [self::FIELD_UPDATED_AT, $this->updatedAt->format("H:i:s")];
        $tasks[] = [self::FIELD_TOTAL, $this->total];

        return PageContentHelper::createTableMd(["Задача", "Результат"], $tasks, "#### День " . $date['yday'] . ". Результаты");
    }

    public function buildProperties(): string
    {
        foreach ($this->taskEntities as $taskEntity) {
            $result = $taskEntity->result;
            if (in_array($taskEntity->category, [CategoryEnum::calories, CategoryEnum::water, CategoryEnum::wakeUp, CategoryEnum::goToBed, CategoryEnum::turnOffAllGadgets], true)) {
                if ($taskEntity->result === 0) {
                    $result = "false";
                } else {
                    $result = "true";
                }
            }

            $properties[] = [$taskEntity->category->value, $result];
        }

        $properties[] = [self::FIELD_CREATED_AT, $this->createdAt->format(self::MD_DATE_TIME_FORMAT)];
        $properties[] = [self::FIELD_UPDATED_AT, $this->updatedAt->format(self::MD_DATE_TIME_FORMAT)];
        $properties[] = [self::FIELD_TOTAL, $this->total];

        return PageContentHelper::createPropertiesBlock($properties ?? []);
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
        return array_filter($page, static function (string $value) use ($minLen) {
            return strlen(trim($value)) > $minLen;
        });
    }

    protected function mapTrim(array $page): array
    {
        return array_map(static function (string $value) {
            return trim($value);
        }, $page);
    }
}