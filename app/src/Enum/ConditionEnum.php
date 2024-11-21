<?php

namespace App\Enum;

enum ConditionEnum: string
{
    case noMore = "не больше";
    case noLess = "не меньше";
    case progressionOn = "прогрессия при выполнении/невыполнении";
    case progressionOff = "без прогресии при выполнении/невыполнении";

    public static function fromString(string $str): self
    {
        foreach (self::cases() as $item) {
            if (self::clearStr($item->name) === self::clearStr($str) || self::clearStr($item->value) === self::clearStr($str)) {
                return $item;
            }
        }
        throw new \RuntimeException("Не найдено значение $str для " . __CLASS__);
    }

    protected static function clearStr(string $str): string
    {
        return strtolower(preg_replace("/[^a-zA-Z0-9а-яА-Я]/u", "", $str));
    }
}