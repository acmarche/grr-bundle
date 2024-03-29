<?php

namespace Grr\GrrBundle\Tests\Periodicity;

use DateTime;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Grr\Core\Tests\BaseTesting;
use Grr\GrrBundle\Area\Factory\AreaFactory;
use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Entity\Periodicity;
use Grr\GrrBundle\Entry\Factory\EntryFactory;
use Grr\GrrBundle\Periodicity\Factory\PeriodicityFactory;
use Grr\GrrBundle\Room\Factory\RoomFactory;

class PeriodicityFactoryTest extends BaseTesting
{
    private EntryFactory $entryFactory;

    private AreaFactory $areaFactory;

    private RoomFactory $roomFactory;

    private PeriodicityFactory $periodicityFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $periodicityFactory = new PeriodicityFactory();
        $this->entryFactory = new EntryFactory($periodicityFactory);
        $this->periodicityFactory = new PeriodicityFactory();
        $this->areaFactory = new AreaFactory();
        $this->roomFactory = new RoomFactory();
    }

    /**
     * @dataProvider getData
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testNew(
        int $year,
        int $month,
        int $day,
        int $hour,
        int $minute
    ): void {
        $this->loadFixtures();

        $area = $this->getArea('Esquare');
        $room = $this->getRoom('Box');

        $entry = $this->entryFactory->initEntryForNew($area, $room, $year, $month, $day);
        $entry->setName('Test');
        $entry->setCreatedBy('Test');

        $this->assertInstanceOf(Entry::class, $entry);
        $periodicity = $this->periodicityFactory->createNew($entry);
        $periodicity->setEndTime(new DateTime('+3 days'));

        $this->assertInstanceOf(Periodicity::class, $entry->getPeriodicity());
        $this->assertInstanceOf(Entry::class, $periodicity->getEntryReference());
        $this->assertSame('Test', $periodicity->getEntryReference()->getName());
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

    protected function loadFixtures(): void
    {
        $files =
            [
                $this->pathFixtures.'area.yaml',
                $this->pathFixtures.'room.yaml',
                $this->pathFixtures.'user.yaml',
                $this->pathFixtures.'authorization.yaml',
            ];

        $this->loader->load($files);
    }
}
