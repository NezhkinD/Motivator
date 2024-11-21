<?php

namespace App\Tests;

use App\Entity\RuleEntity;
use App\Helper\PageCreator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SimpleTest extends KernelTestCase
{
    /**
     * @throws \JsonException
     */
    public function testSomething(): void
    {

        $pageCreator = new PageCreator();
        file_put_contents(__DIR__ . "/test.md", $pageCreator->createTodoPageContent(new \DateTime()));


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