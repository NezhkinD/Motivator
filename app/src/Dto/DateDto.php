<?php

namespace App\Dto;

use App\Helper\StringExploder;

class DateDto
{
    public const string PARAM_CREATED_AT = 'created_at';
    public const string PARAM_UPDATED_AT = 'updated_at';

    public string $name;
    public \DateTime $value;

    public static function fromStr(string $str): self
    {
        $self = new self();
        $explode = StringExploder::explode($str);
        if ($explode[0] === $self::PARAM_CREATED_AT || $explode[0] === $self::PARAM_UPDATED_AT) {
            $self->name = $explode[0];
        } else {
            throw new \RuntimeException("Для формирования DateDto, необходимо передать строку, которая будет содержать " . self::PARAM_CREATED_AT . " или " . self::PARAM_UPDATED_AT);
        }

        if (is_a($explode[1], \DateTime::class)) {
            $self->value = $explode[1];
        } else {
            throw new \RuntimeException("Для формирования DateDto, необходимо передать строку, которая будет содержать дату");
        }

        return $self;
    }
}