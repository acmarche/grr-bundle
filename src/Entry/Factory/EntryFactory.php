<?php

namespace Grr\GrrBundle\Entry\Factory;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Entity\Periodicity;
use Grr\GrrBundle\Entity\Room;
use Grr\GrrBundle\Periodicity\Factory\PeriodicityFactory;

class EntryFactory
{
    /**
     * @var PeriodicityFactory
     */
    private $periodicityFactory;

    public function __construct(PeriodicityFactory $periodicityFactory)
    {
        $this->periodicityFactory = $periodicityFactory;
    }

    public function generateEntry(Entry $entry, CarbonInterface $carbon): Entry
    {
        $newEntry = clone $entry;

        $startTime = Carbon::instance($entry->getStartTime());
        $startTime->setYear($carbon->year);
        $startTime->setMonth($carbon->month);
        $startTime->setDay($carbon->day);

        $endTime = Carbon::instance($entry->getEndTime());
        $endTime->setYear($carbon->year);
        $endTime->setMonth($carbon->month);
        $endTime->setDay($carbon->day);

        $newEntry->setStartTime($startTime->toDateTime());
        $newEntry->setEndTime($endTime->toDateTime());

        return $newEntry;
    }

    public function createNew(): Entry
    {
        return new Entry();
    }

    public function initEntryForNew(
        Area $area,
        Room $room,
        \DateTimeInterface $date,
        int $hour,
        int $minute
    ): Entry {
        $dateSelected = Carbon::instance($date);
        if ($hour > 0) {
            $dateSelected->hour($hour);
            $dateSelected->minute($minute);
        } else {
            $dateSelected->hour($area->getStartTime());
        }
        $entry = $this->createNew();
        $entry->setArea($area);
        $entry->setRoom($room);
        $entry->setStartTime($dateSelected);
        $endTime = $dateSelected->copy()->addMinutes($area->getDurationDefaultEntry());
        $entry->setEndTime($endTime);
        $entry->setPeriodicity($this->initPeriodicity($entry));

        return $entry;
    }

    protected function initPeriodicity(Entry $entry): Periodicity
    {
        $periodicity = $this->periodicityFactory->createNew($entry);
        $periodicity->setEndTime($entry->getStartTime());
        $periodicity->setType(0);

        return $periodicity;
    }
}
