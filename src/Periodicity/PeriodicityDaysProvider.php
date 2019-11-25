<?php

namespace Grr\GrrBundle\Periodicity;

use DateTimeInterface;
use DateTime;
use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Entity\Periodicity;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;

class PeriodicityDaysProvider
{
    /**
     * @var CarbonInterface
     */
    private $periodicity_end;
    /**
     * @var CarbonInterface
     */
    private $entry_start;

    /**
     * @return array|CarbonPeriod
     */
    public function getDaysByEntry(Entry $entry)
    {
        $periodicity = $entry->getPeriodicity();

        if (null === $periodicity) {
            return [];
        }

        return $this->getDaysByPeriodicity($periodicity, $entry->getStartTime());
    }

    /**
     * @return \Carbon\CarbonPeriod|mixed[]
     */
    public function getDaysByPeriodicity(
        Periodicity $periodicity,
        DateTimeInterface $startTime
    ) {
        $typePeriodicity = $periodicity->getType();

        $this->entry_start = Carbon::instance($startTime);
        $this->periodicity_end = Carbon::instance($periodicity->getEndTime());

        if (PeriodicityConstant::EVERY_DAY === $typePeriodicity) {
            return $this->forEveryDays();
        }

        if (PeriodicityConstant::EVERY_YEAR === $typePeriodicity) {
            return $this->forEveryYears();
        }

        if (PeriodicityConstant::EVERY_MONTH_SAME_DAY === $typePeriodicity) {
            return $this->forEveryMonthSameDay();
        }

        if (PeriodicityConstant::EVERY_MONTH_SAME_WEEK_DAY === $typePeriodicity) {
            return $this->forEveryMonthSameDayOfWeek();
        }

        if (PeriodicityConstant::EVERY_WEEK === $typePeriodicity) {
            return $this->forEveryWeek($periodicity);
        }

        return [];
    }

    protected function forEveryDays(): CarbonPeriod
    {
        $period = CarbonPeriod::create(
            $this->entry_start->toDateString(),
            $this->periodicity_end->toDateString(),
            CarbonPeriod::EXCLUDE_START_DATE
        );

        return $this->applyFilter($period);
    }

    protected function forEveryYears(): CarbonPeriod
    {
        $period = Carbon::parse($this->entry_start->toDateString())->yearsUntil(
            $this->periodicity_end->toDateString()
        );

        return $this->applyFilter($period);
    }

    /**
     * Par exemple 12-08, 12-09 12-10, 12-11...
     */
    protected function forEveryMonthSameDay(): CarbonPeriod
    {
        $period = Carbon::parse($this->entry_start->toDateString())->daysUntil(
            $this->periodicity_end->toDateString()
        );

        $filter = function ($date): bool {
            return $date->day === $this->entry_start->day;
        };

        return $this->applyFilter($period, $filter);
    }

    /**
     * Par exemple le premier samedi de chaque mois.
     */
    private function forEveryMonthSameDayOfWeek(): CarbonPeriod
    {
        $period = Carbon::parse($this->entry_start->toDateString())->daysUntil(
            $this->periodicity_end->toDateString()
        );

        $filter = function ($date): bool {
            return $date->dayOfWeek === $this->entry_start->dayOfWeek && $date->weekOfMonth === $this->entry_start->weekOfMonth;
        };

        return $this->applyFilter($period, $filter);
    }

    /**
     * toutes les 1,2,3,4,5 semaines
     * lundi, mardi, mercredi...
     */
    protected function forEveryWeek(Periodicity $periodicity): CarbonPeriod
    {
        /**
         * monday, tuesday, wednesday.
         *
         * @example : [1,2,3]
         */
        $days = $periodicity->getWeekDays();
        /**
         * @example 1 for every weeks, 2 every 2 weeks, 3,4...
         */
        $repeat_week = $periodicity->getWeekRepeat();

        /**
         * filter days of the week.
         *
         * @param $date
         *
         * @return bool
         */
        $filterDayOfWeek = function ($date) use ($days): bool {
            return in_array($date->dayOfWeekIso, $days, true);
        };

        /**
         * Carbon::class
         * $this->entry_start
         * $this->periodicity_end.
         */
        $period = Carbon::parse($this->entry_start->toDateString())->daysUntil(
            $this->periodicity_end->toDateString()
        );

        /**
         * filter every x weeks.
         *
         * @param $date
         *
         * @return bool
         */
        $filterWeek = function ($date) use ($repeat_week): bool {
            return 0 === $date->weekOfYear % $repeat_week;
        };

        $period->excludeStartDate();
        $period->addFilter($filterDayOfWeek);

        if ($repeat_week > 1) {
            $period->addFilter($filterWeek);
        }

        return $period;
    }

    /**
     * @throws \Exception
     *
     * @todo replace for every week
     * https://stackoverflow.com/questions/57479939/php-carbon-every-monday-and-tuesday-every-2-weeks/57506714#57506714
     */
    protected function brouillon(): void
    {
        $start = new DateTime('now');
        $end = clone $start;
        $end->modify('+4 month');
        $days = ['Monday', 'Tuesday'];
        foreach (CarbonPeriod::create($start, CarbonInterval::weeks(2), $end, CarbonPeriod::IMMUTABLE) as $baseDate) {
            foreach ($days as $dayName) {
                $date = $baseDate->is($dayName) ? $baseDate : $baseDate->next($dayName);
            }
        }
    }

    protected function applyFilter(CarbonPeriod $period, callable $filter = null): CarbonPeriod
    {
        $period->excludeStartDate();

        if ($filter) {
            $period->addFilter($filter);
        }

        return $period;
    }
}
