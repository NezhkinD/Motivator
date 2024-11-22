<?php

namespace App\Dto;

use App\Helper\StringExploder;

class LineDto
{
    public string $name;
    public int $value;

    public static function fromStr(string $str): self
    {
        $self = new self();
        $explode = StringExploder::explode($str);
        $self->name = $explode[0];

        if (is_int($explode[1])) {
            $self->value = $explode[1];
        } else {
            throw new \RuntimeException("Для формирования LineDto необходимо передать сторку, которая будет содержать числовое значение");
        }

        return $self;
    }
}