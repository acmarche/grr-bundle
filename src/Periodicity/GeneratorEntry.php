<?php

namespace Grr\GrrBundle\Periodicity;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Grr\GrrBundle\Entity\Entry;

class GeneratorEntry
{
    public function generateEntry(Entry $entry, CarbonInterface $day): Entry
    {
        $newEntry = clone $entry;

        $startTime = Carbon::instance($entry->getStartTime());
        $startTime->setYear($day->year);
        $startTime->setMonth($day->month);
        $startTime->setDay($day->day);

        $endTime = Carbon::instance($entry->getEndTime());
        $endTime->setYear($day->year);
        $endTime->setMonth($day->month);
        $endTime->setDay($day->day);

        $newEntry->setStartTime($startTime->toDateTime());
        $newEntry->setEndTime($endTime->toDateTime());

        return $newEntry;
    }
}
