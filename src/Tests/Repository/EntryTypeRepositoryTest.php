<?php

namespace Grr\GrrBundle\Tests\Repository;

use Grr\GrrBundle\Entity\TypeEntry;
use Grr\Core\Tests\BaseTesting;

class TypeEntryRepositoryTest extends BaseTesting
{
    public function testFindByName(): void
    {
        $this->loadFixtures();
        $typeEntry = $this->entityManager
            ->getRepository(TypeEntry::class)
            ->findOneBy(['name' => 'Cours']);

        $this->assertEquals('Cours', $typeEntry->getName());
        $this->assertEquals('A', $typeEntry->getLetter());
    }

    protected function loadFixtures(): void
    {
        $files =
            [
                $this->pathFixtures.'entry_type.yaml',
            ];

        $this->loader->load($files);
    }
}
