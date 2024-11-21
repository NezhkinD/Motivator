<?php

namespace App\Enum;

enum DaysEnum: string
{
    case none = "none";
    case all = "все дни недели";
    case weekdayOnly = "только будни";
    case weekendsOnly = "только выходные";

    public static function fromString(string $str): self
    {
        foreach (self::cases() as $item) {
            if (self::clearStr($item->name) === self::clearStr($str) || self::clearStr($item->value) === self::clearStr($str)) {
                return $item;
            }
        }
        return self::none;
    }

    protected static function clearStr(string $str): string
    {
        return strtolower(preg_replace("/[^a-zA-Z0-9а-яА-Я]/u", "", $str));
    }
}