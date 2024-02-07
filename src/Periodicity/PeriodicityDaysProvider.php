<?php

namespace Grr\GrrBundle\Periodicity;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Grr\Core\Contrat\Entity\PeriodicityInterface;
use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Entity\Periodicity;

class PeriodicityDaysProvider
{
    /**
     * @var DateTime|DateTimeImmutable|null
     */
    private ?DateTimeInterface $periodicity_end = null;

    /**
     * @var DateTime|DateTimeImmutable|null
     */
    private ?DateTimeInterface $entry_start = null;

    public function getDaysByEntry(Entry $entry): array|CarbonPeriod
    {
        $periodicity = $entry->getPeriodicity();

        if (! $periodicity instanceof PeriodicityInterface) {
            return [];
        }

        return $this->getDaysByPeriodicity($periodicity, $entry->getStartTime());
    }

    public function getDaysByPeriodicity(
        Periodicity $periodicity,
        DateTimeInterface $dateTime
    ): array|CarbonPeriod {
        $typePeriodicity = $periodicity->getType();

        $this->entry_start = Carbon::instance($dateTime);
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
        $carbonPeriod = CarbonPeriod::create(
            $this->entry_start->toDateString(),
            $this->periodicity_end->toDateString(),
            CarbonPeriod::EXCLUDE_START_DATE
        );

        return $this->applyFilter($carbonPeriod);
    }

    protected function forEveryYears(): CarbonPeriod
    {
        $carbonPeriod = Carbon::parse($this->entry_start->toDateString())->yearsUntil(
            $this->periodicity_end->toDateString()
        );

        return $this->applyFilter($carbonPeriod);
    }

    /**
     * Par exemple 12-08, 12-09 12-10, 12-11...
     */
    protected function forEveryMonthSameDay(): CarbonPeriod
    {
        $carbonPeriod = Carbon::parse($this->entry_start->toDateString())->daysUntil(
            $this->periodicity_end->toDateString()
        );

        $filter = fn ($date): bool => $date->day === $this->entry_start->day;

        return $this->applyFilter($carbonPeriod, $filter);
    }

    /**
     * Par exemple le premier samedi de chaque mois.
     */
    private function forEveryMonthSameDayOfWeek(): CarbonPeriod
    {
        $period = Carbon::parse($this->entry_start->toDateString())->daysUntil(
            $this->periodicity_end->toDateString()
        );

        $filter = fn ($date): bool => $date->dayOfWeek === $this->entry_start->dayOfWeek && $date->weekOfMonth === $this->entry_start->weekOfMonth;

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
        $filterDayOfWeek = static fn($date): bool => \in_array($date->dayOfWeekIso, $days, true);

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
        $filterWeek = static fn($date): bool => 0 === $date->weekOfYear % $repeat_week;

        $period->excludeStartDate();
        $period->addFilter($filterDayOfWeek);

        if ($repeat_week > 1) {
            $period->addFilter($filterWeek);
        }

        return $period;
    }

    /**
     * @throws Exception
     *
     * @todo replace for every week
     * https://stackoverflow.com/questions/57479939/php-carbon-every-monday-and-tuesday-every-2-weeks/57506714#57506714
     * https://github.com/briannesbitt/Carbon/pull/1986
     */
    protected function brouillon(): void
    {
        $dateTime = new DateTime('now');
        $end = clone $dateTime;
        $end->modify('+4 month');

        $days = ['Monday', 'Tuesday'];
        foreach (CarbonPeriod::create($dateTime, CarbonInterval::weeks(2), $end, CarbonPeriod::IMMUTABLE) as $baseDate) {
            foreach ($days as $dayName) {
                $date = $baseDate->is($dayName) ? $baseDate : $baseDate->next($dayName);
            }
        }
    }

    protected function applyFilter(CarbonPeriod $carbonPeriod, callable $filter = null): CarbonPeriod
    {
        $carbonPeriod->excludeStartDate();

        if ($filter !== null) {
            $carbonPeriod->addFilter($filter);
        }

        return $carbonPeriod;
    }
}
