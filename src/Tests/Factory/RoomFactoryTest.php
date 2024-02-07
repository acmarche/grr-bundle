<?php

namespace Grr\GrrBundle\Tests\Factory;

use Grr\Core\Tests\BaseTesting;
use Grr\GrrBundle\Area\Factory\AreaFactory;
use Grr\GrrBundle\Entity\Room;
use Grr\GrrBundle\Room\Factory\RoomFactory;

class RoomFactoryTest extends BaseTesting
{
    private AreaFactory $areaFactory;

    private RoomFactory $roomFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->areaFactory = new AreaFactory();
        $this->roomFactory = new RoomFactory();
    }

    public function testCreateNew(): void
    {
        $area = $this->areaFactory->createNew();
        $area->setName('Lulu');

        $room = $this->roomFactory->createNew($area);

        $this->assertInstanceOf(Room::class, $room);
        $this->assertSame('Lulu', $room->getArea()->getName());
    }
}
