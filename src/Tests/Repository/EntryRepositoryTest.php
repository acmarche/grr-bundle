<?php

namespace Grr\GrrBundle\Tests\Repository;

use Carbon\Carbon;
use Grr\Core\Tests\BaseTesting;
use Grr\GrrBundle\Entity\Entry;

class EntryRepositoryTest extends BaseTesting
{
    public function testFindForMonth(): void
    {
        $this->loadFixtures();

        $month = Month::init(2019, 8, 'fr');
        $area = $this->getArea('Esquare');

        $entries = $this->entityManager
            ->getRepository(Entry::class)
            ->findForMonth($month->toDateTime(), $area);

        $count = is_countable($entries) ? \count($entries) : 0;

        $this->assertEquals(4, $count);
    }

    /**
     * @dataProvider dataForDay
     */
    public function testFindForDay(int $year, int $month, int $day, string $room, int $count, string $title): void
    {
        $this->loadFixtures();

        $day = Carbon::create($year, $month, $day);
        $room = $this->getRoom($room);

        $entries = $this->entityManager
            ->getRepository(Entry::class)
            ->findForDay($day, $room);

        $countResult = is_countable($entries) ? \count($entries) : 0;
        $this->assertEquals($count, $countResult);

        $this->assertSame($title, $entries[0]->getName());
    }

    /**
     * La date de debut de la nouvelle entry est plus grande que
     * la date de début d'une entry existante.
     */
    public function testBusyDateBeginGreaterThanStartTime(): void
    {
        $this->loadFixtures(true);

        $entry = $this->getEntry('Réunion occupée par Henallux');

        //pour avoir un room existant en db
        $room = $this->getRoom($entry->getRoom()->getName());

        $entries = $this->entityManager
            ->getRepository(Entry::class)
            ->isBusy($entry, $room);

        foreach ($entries as $result) {
            $this->assertSame('Réunion henalux', $result->getName());
        }
    }

    /**
     * La date de fin de la nouvelle entry est plus petite que
     * la date de fin d'une entry existante.
     */
    public function testBusyDateEndIsSmallerThanEndTime(): void
    {
        $this->loadFixtures(true);

        $entry = $this->getEntry('Réunion occupée par détente');

        //pour avoir un room existant en db
        $room = $this->getRoom($entry->getRoom()->getName());

        $entries = $this->entityManager
            ->getRepository(Entry::class)
            ->isBusy($entry, $room);

        foreach ($entries as $result) {
            $this->assertSame('Réunion détente', $result->getName());
        }
    }

    public function dataForDay(): array
    {
        return [
            [
                'year' => 2019,
                'month' => 8,
                'day' => 20,
                'room' => 'Relax Room',
                'count' => 1,
                'title' => 'Réunion pssp',
            ],
            [
                'year' => 2019,
                'month' => 9,
                'day' => 19,
                'room' => 'Salle Conseil',
                'count' => 1,
                'title' => 'Réunion conseillers',
            ],
        ];
    }

    protected function loadFixtures($withBusy = false): void
    {
        $files =
            [
                $this->pathFixtures.'area.yaml',
                $this->pathFixtures.'room.yaml',
                $this->pathFixtures.'entry_type.yaml',
                $this->pathFixtures.'entry.yaml',
            ];

        if ($withBusy) {
            $files[] = $this->pathFixtures.'entry_busy.yaml';
        }

        $this->loader->load($files);
    }
}
