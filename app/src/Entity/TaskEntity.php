<?php

namespace App\Entity;

use App\Enum\CategoryEnum;

class TaskEntity
{
    public CategoryEnum $category;

    /** Текущее значение */
    public int $result;

    /** Начисленные баллы, исходя из result и коэффицентов */
    public float $score;

    /** Описание задачи */
    public ?string $description = null;

    /**
     * @throws \Exception
     */
    public static function create(CategoryEnum $categoryEnum, int $result, int $score, ?string $description = null): self
    {
        $self = new self();
        $self->category = $categoryEnum;
        $self->result = $result;
        $self->score = $score;
        $self->description = $description;
        return $self;
    }
}