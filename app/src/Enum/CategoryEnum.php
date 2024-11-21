<?php

namespace App\Enum;

enum CategoryEnum: string
{
    case water = "вода";
    case wakeUp = "подъем в 06:30";
    case goToBed = 'отбой в 22:30';
    case turnOffAllGadgets = "выключить все гаджеты в 22:00";
    case calories = 'калории';
    case trainingPlaceNearHome = 'места у дома (тренировка)';
    case trainingPool = 'бассейн (тренировка)';
    case hookah = 'кальян';
    case redBull = 'red bull';
    case coolCola = 'cool cola';
    case vitamins = 'витамины';
    case steps = 'шаги';
    case compressionStockings = 'копрессионные чулки';
    case pressureMeasurement = 'измерение давления';
    case hygiene = 'гигиена';
    case faceCare = 'уход за лицом';
    case brushTeeth = 'чистка зубов';
    case readingImgBook = 'чтение худ. лит-ры';
    case readingTechBook = 'чтение тех. лит-ры';
    case eduMephi = 'обучение мифи';
    case personalProj = 'личные_проекты';
    case turnRoboVacuum = 'включить робот-пылесос';
    case washDish = 'помыть посуду';
    case washFloor = 'помыть полы';
    case washClothes = 'постирать белье';
    case doTaskFromPlan = 'выполнить задачи из плана';
    case vegetablesInFood = 'овощи в пище';
    case sauna = 'сауна и хамам';
    case aeroEx = 'аэробные нагрузки';
    case stretching = 'растяжка';

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