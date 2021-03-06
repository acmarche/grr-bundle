<?php

namespace Grr\GrrBundle\Booking;

use Grr\GrrBundle\Entity\Entry;

class ApiSerializer
{
    public function serializeEntry(Entry $entry, bool $hours): array
    {
        $format = 'Y-m-d';
        if ($hours) {
            $format .= ' H:i';
        }

        return [
            'name' => $entry->getName(),
            'startTime' => $entry->getStartTime()->format($format),
            'endTime' => $entry->getEndTime()->format($format),
        ];
    }

    public function serializeEntries(array $entries, bool $hours = false): array
    {
        $data = [];
        foreach ($entries as $entry) {
            $data[] = $this->serializeEntry($entry, $hours);
        }

        return $data;
    }
}
