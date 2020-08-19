<?php

namespace Grr\GrrBundle\Tests\Factory;

use Grr\GrrBundle\Area\Factory\AreaFactory;
use Grr\GrrBundle\Entity\Room;
use Grr\Core\Tests\BaseTesting;
use Grr\GrrBundle\Room\Factory\RoomFactory;

class RoomFactoryTest extends BaseTesting
{
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
