<?php

namespace App\Enum;

enum CategoryEnum: string
{
    case water = "вода";
    case wakeUp = "подъем в 06-30";
    case goToBed = 'отбой в 22-30';
    case turnOffAllGadgets = "выключить все гаджеты в 22-00";
    case calories = 'калории';
    case trainingPlaceNearHome = 'места у дома (тренировка)';
    case trainingPool = 'бассейн (тренировка)';
    case hookah = 'кальян';
    case redBull = 'red bull';
    case coolCola = 'cool cola';
    case vitamins = 'витамины';
    case steps = 'шаги';
    case compressionStockings = 'надеть копрессионные чулки';
    case pressureMeasurement = 'измерить давление';
    case hygiene = 'гигиена';
    case faceCare = 'уход за лицом';
    case brushTeeth = 'чистка зубов';
    case readingImgBook = 'чтение худ. лит-ры';
    case readingTechBook = 'чтение тех. лит-ры';
    case eduMephi = 'обучение МИФИ';
    case personalProj = 'личные проекты';
    case turnRoboVacuum = 'включить робот-пылесос';
    case washDish = 'помыть посуду';
    case washFloor = 'помыть полы';
    case washClothes = 'постирать белье';
    case doTaskFromPlan = 'выполнить задачи из плана';
    case vegetablesInFood = 'овощи в пище';
    case sauna = 'сауна и хамам';
    case aeroEx = 'аэробные нагрузки';
    case stretching = 'растяжка';
    case solar = 'солярий';
    case taskFromCalendar = 'выполнение задачи из TODO';
    case washTech = 'постирать тряпки для пылесоса';
    case vacuumSofa = 'пропылесосить диван';
    case clean = 'почистиь лоток кота';
    case hardening = 'закаливание';
    case total = 'total';


    public static function fromString(string $str): self
    {
        $enum = self::find($str);
        if (self::find($str) !== null) {
            return $enum;
        }

        throw new \RuntimeException("Не найдено значение <$str> для " . __CLASS__);
    }

    public static function has(string $str): bool
    {
        return self::find($str) !== null;
    }

    protected static function find(string $str): ?CategoryEnum
    {
        foreach (self::cases() as $item) {
            if (strripos(self::clearStr($str), self::clearStr($item->name)) !== false) {
                return $item;
            }

            if (strripos(self::clearStr($str), self::clearStr($item->value)) !== false) {
                return $item;
            }
        }

        return null;
    }

    protected static function clearStr(string $str): string
    {
        return strtolower(preg_replace("/[^a-zA-Z0-9а-яА-Я]/u", "", $str));
    }
}