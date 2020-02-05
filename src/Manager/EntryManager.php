<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 1/03/19
 * Time: 19:59.
 */

namespace Grr\GrrBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Entity\Periodicity;
use Grr\GrrBundle\Repository\EntryRepository;

class EntryManager extends BaseManager
{
    /**
     * @var EntryRepository
     */
    private $entryRepository;

    public function __construct(EntityManagerInterface $entityManager, EntryRepository $entryRepository)
    {
        parent::__construct($entityManager);
        $this->entryRepository = $entryRepository;
    }

    public function removeEntriesByPeriodicity(Periodicity $periodicity, Entry $entryToSkip): void
    {
        foreach ($this->entryRepository->findByPeriodicity($periodicity) as $entry) {
            if ($entry->getId() !== $entryToSkip->getId()) {
                $this->remove($entry);
            }
        }
    }
}
