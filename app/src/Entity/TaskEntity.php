<?php

namespace App\Entity;

use App\Enum\CategoryEnum;
use DateTime;

class TaskEntity
{
    public const FIELD_CREATED_AT = 'created_at';
    public const FIELD_UPDATED_AT = 'updated_at';

    public CategoryEnum $category;
    public int $result;
    public \DateTime $createdAt;
    public \DateTime $updatedAt;

    /**
     * @throws \Exception
     */
    public static function create(CategoryEnum $categoryEnum, int $result, DateTime $createdAt, DateTime $updatedAt): self
    {
        $self = new self();
        $self->category = $categoryEnum;
        $self->result = $result;
        $self->createdAt = $createdAt;
        $self->updatedAt = $updatedAt;
        return $self;
    }
}