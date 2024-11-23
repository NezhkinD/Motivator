<?php

namespace App\Helper;

use App\Dto\DateDto;
use App\Enum\CategoryEnum;
use DateTime;

class PageContentHelper
{
    protected const string MD_DATE_TIME_FORMAT = 'Y-m-dTH:i:s';

    public function createProperties(): string
    {

    }

    protected static function buildListMd(string $name, $value): string
    {
        return "- $name: $value";
    }


    protected static function createLinesMd(array $properties): string
    {
        return "\n" . implode("\n", $properties) . "\n";
    }

    protected static function buildLineMd(string $name, $value): string
    {
        return $name . ": " . $value;
    }

    /**
     * @param string[][] $properties
     * @return string
     */
    public static function createPropertiesBlock(array $properties): string
    {
        $result = [];
        foreach ($properties as $line){
            $result[] = self::buildLineMd($line[0], $line[1]);
        }

        return "---\n" . implode("\n", $result) . "\n---\n";
    }

    /**
     * @param string[] $tableHeaders
     * @param string[][] $tableLines
     * @param string|null $blockHead
     * @return string
     */
    public static function createTableMd(array $tableHeaders, array $tableLines, ?string $blockHead = null): string
    {
        if ($blockHead) {
            $lines[] = $blockHead;
        }
        $lines[] = "| " . implode(" | ", $tableHeaders) . " |";
        $lines[] = "| ---| --- |";
        foreach ($tableLines as $property) {
            $lines[] = "| " . implode(" | ", $property) . " |";
        }

        return PHP_EOL . implode(PHP_EOL, $lines);
    }
}