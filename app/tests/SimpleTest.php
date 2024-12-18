<?php

namespace App\Tests;

use App\Entity\RuleEntity;
use App\Entity\TodoPageEntity;
use App\Enum\CategoryEnum;
use App\Helper\PageHelper;
use App\Helper\ScoreCounter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * php bin/phpunit
 */
class SimpleTest extends KernelTestCase
{
    /**
     * @throws \JsonException
     */
    public function testSomething(): void
    {
        $dir = __DIR__ . "/../config/allRules.json";
        $rules = json_decode(file_get_contents($dir), true, 512, JSON_THROW_ON_ERROR);

        $page = "
        вода: true\n
        подъем в 06:30: true\n
        отбой в 22:30: true\n
        выключить все гаджеты в 22:00: true\n
        калории: true\n
        места у дома (тренировка): true\n
        кальян: 0\n
        red bull: 0\n
        cool cola: 1\n
        шаги: 10000\n
        created_at: 2024-11-21T17:01:00\n
        updated_at: 2024-11-21T17:01:00\n
        total: 0\n
        ";
        $explode = explode("\n", $page);
        $todoPageEntity = TodoPageEntity::fromData($rules, [$explode]);
        $scoreCounter = new ScoreCounter();
        $countTotal = $scoreCounter->countTotal($todoPageEntity, [$todoPageEntity]);

        dd($countTotal->total);




        $explodeStr = $taskRuleEntity->explodeStr("updated_at: 2024-12-21");
        dd($explodeStr);

        $categoryEnum = CategoryEnum::fromString("чтение лит-ры");
        dd(
            $categoryEnum
        );


        $pageCreator = new PageHelper();
        file_put_contents(__DIR__ . "/test.md", $pageCreator->createNewTodoPageContent(new \DateTime()));


        dd(11);

        $dir = __DIR__ . "/../config/allRules.json";
        $array = json_decode(file_get_contents($dir), true, 512, JSON_THROW_ON_ERROR);

        $objs = [];
        foreach ($array as $rule) {
            $objs[] = RuleEntity::createFromArray($rule);
        }

        dd($objs);
    }
}