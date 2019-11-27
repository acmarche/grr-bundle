<?php

namespace Grr\GrrBundle\Tests\Factory;

use Grr\GrrBundle\Area\AreaFactory;
use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Tests\BaseTesting;

class AreaFactoryTest extends BaseTesting
{
    /**
     * @var AreaFactory
     */
    private $areaFactory;

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
