<?php

namespace Grr\GrrBundle\Tests\Factory;

use Grr\Core\Tests\BaseTesting;
use Grr\GrrBundle\Area\Factory\AreaFactory;
use Grr\GrrBundle\Entity\Area;

class AreaFactoryTest extends BaseTesting
{
    private AreaFactory $areaFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->areaFactory = new AreaFactory();
    }

    public function testCreateNew(): void
    {
        $area = $this->areaFactory->createNew();
        $area->setName('Lolo');

        $this->assertInstanceOf(Area::class, $area);
        $this->assertSame('Lolo', $area->getName());
    }
}
