<?php

namespace Grr\GrrBundle\Periodicity;

class PeriodicityConstant
{
    public const NONE = 0;
    public const EVERY_DAY = 1;
    public const EVERY_WEEK = 2;
    public const EVERY_YEAR = 4;
    public const EVERY_MONTH_SAME_DAY = 3;
    public const EVERY_MONTH_SAME_WEEK_DAY = 5;

    /**
     * clef de type rep_type_0,rep_type_1,...
     *
     * @return string[]
     */
    public static function getTypesPeriodicite(): array
    {
        $vocab = [];
        $vocab[self::NONE] = 'periodicity.type.none';
        $vocab[self::EVERY_DAY] = 'periodicity.type.everyday';
        $vocab[self::EVERY_WEEK] = 'periodicity.type.everyweek';
        $vocab[self::EVERY_MONTH_SAME_DAY] = 'periodicity.type.everymonth.sameday';
        $vocab[self::EVERY_MONTH_SAME_WEEK_DAY] = 'periodicity.type.everymonth.sameweek';
        $vocab[self::EVERY_YEAR] = 'periodicity.type.everyyear';

        //$vocab[6] =>'periodicity.type.cycle.days');

        return $vocab;
    }

    public static function getTypePeriodicite(int $type): int|string
    {
        return self::getTypesPeriodicite()[$type] ?? $type;
    }
}
