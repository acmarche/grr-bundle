<?php

namespace Grr\GrrBundle\Tests\Entry;

use Carbon\Carbon;
use DateTimeInterface;
use Grr\GrrBundle\Area\Factory\AreaFactory;
use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Entity\Room;
use Grr\Core\Tests\BaseTesting;
use Grr\GrrBundle\Entry\Factory\EntryFactory;
use Grr\GrrBundle\Periodicity\Factory\PeriodicityFactory;
use Grr\GrrBundle\Room\Factory\RoomFactory;

class EntryFactoryTest extends BaseTesting
{
    /**
     * @var EntryFactory
     */
    private $entryFactory;
    /**
     * @var AreaFactory
     */
    private $areaFactory;
    /**
     * @var RoomFactory
     */
    private $roomFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $periodicityFactory = new PeriodicityFactory();
        $this->entryFactory = new EntryFactory($periodicityFactory);
        $this->areaFactory = new AreaFactory();
        $this->roomFactory = new RoomFactory();
    }

    public function testCreateNew(): void
    {
        $entry = $this->entryFactory->createNew();
        $this->assertInstanceOf(Entry::class, $entry);
    }

    /**
     * @dataProvider getData
     *
     * @param Area $area
     * @param Room $room
     */
    public function testInitEntryForNew(
        int $year,
        int $month,
        int $day,
        int $hour,
        int $minute
    ): void {
        $area = $this->areaFactory->createNew();
        $area->setName('Area1');
        $room = $this->roomFactory->createNew($area);
        $room->setName('Salle1');

        $date = Carbon::create($year, $month, $day, $hour, $minute);
        $endTime = $date->copy()->addMinutes($area->getDurationDefaultEntry());

        $entry = $this->entryFactory->initEntryForNew($area, $room, $year, $month, $day, $hour, $minute);

        $this->assertInstanceOf(Entry::class, $entry);
        $this->assertSame('Area1', $entry->getArea()->getName());
        $this->assertSame('Salle1', $entry->getRoom()->getName());
        $this->assertInstanceOf(DateTimeInterface::class, $entry->getStartTime());
        $this->assertInstanceOf(DateTimeInterface::class, $entry->getEndTime());
        $this->assertSame("$year $month $day $hour $minute", $entry->getStartTime()->format('Y n j G i'));
        $this->assertSame($endTime->format('Y n j G i'), $entry->getEndTime()->format('Y n j G i'));
    }

    public function getData(): array
    {
        return [
            [2019, 8, 19, 10, 12],
            [2019, 2, 28, 9, 24],
            [2019, 12, 31, 14, 30],
            [2019, 6, 30, 16, 20],
        ];
    }
}
