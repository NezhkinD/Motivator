<?php

namespace App\Helper;

class StringExploder
{
    protected const REGEX_PATTERN_DATETIME = "/20(2[4-9]|[3-9][0-9])-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])T([0-1][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])/";
    protected const REGEX_PATTERN_DATE = "/20(2[4-9]|[3-9][0-9])-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])/";

    public static function explode(string $str): array
    {
        $explode = explode(":", $str, 2);

        $name = trim($explode[0]);
        if (count($explode) !== 2) {
            $result = 0;
            return [$name, $result];
        }

        $value = trim($explode[1]);

        if ($value === "true") {
            $result = 1;

        } elseif ($value === "false") {
            $result = 0;

        } elseif (is_numeric($value)) {
            $result = (int)$value;

        } elseif (self::hasMatch(self::REGEX_PATTERN_DATETIME, $value)) {
            $result = new \DateTime($value);

        } elseif (self::hasMatch(self::REGEX_PATTERN_DATE, $value)) {
            $result = new \DateTime($value);

        } else {
            $result = 0;
        }

        return [$name, $result];
    }

    protected static function hasMatch(string $regex, string $str): bool
    {
        preg_match_all($regex, $str, $matches);
        return count($matches[0]) > 0;
    }
}