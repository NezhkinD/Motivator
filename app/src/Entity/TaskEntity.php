<?php

namespace App\Entity;

use App\Enum\CategoryEnum;
use DateTime;

class TaskEntity
{
    public CategoryEnum $category;
    public int $result;

    /**
     * @throws \Exception
     */
    public static function create(CategoryEnum $categoryEnum, int $result): self
    {
        $self = new self();
        $self->category = $categoryEnum;
        $self->result = $result;
        return $self;
    }
}