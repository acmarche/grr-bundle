<?php

namespace Grr\GrrBundle\Tests\Repository;

use Grr\Core\Tests\BaseTesting;
use Grr\GrrBundle\Entity\Area;

class AreaRepositoryTest extends BaseTesting
{
    public function testSearchByName(): void
    {
        $this->loader->load(
            [
                $this->pathFixtures.'area.yaml',
            ]
        );

        $area = $this->getArea('Esquare');

        $this->assertEquals('Esquare', $area->getName());
    }

    public function testCountArea(): void
    {
        $this->loader->load(
            [
                $this->pathFixtures.'area.yaml',
            ]
        );

        $result = $this->entityManager->getRepository(Area::class)->findAll();

        $this->assertEquals(2, \count($result));
    }
}
