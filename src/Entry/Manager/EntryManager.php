<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 1/03/19
 * Time: 19:59.
 */

namespace Grr\GrrBundle\Entry\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Grr\Core\Contrat\Entity\EntryInterface;
use Grr\Core\Contrat\Entity\PeriodicityInterface;
use Grr\Core\Contrat\Repository\EntryRepositoryInterface;
use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Entity\Periodicity;
use Grr\GrrBundle\Entry\Repository\EntryRepository;
use Grr\GrrBundle\Manager\BaseManager;

class EntryManager extends BaseManager
{
    /**
     * @var EntryRepository
     */
    private $entryRepository;

    public function __construct(EntityManagerInterface $entityManager, EntryRepositoryInterface $entryRepository)
    {
        parent::__construct($entityManager);
        $this->entryRepository = $entryRepository;
    }

    public function removeEntriesByPeriodicity(PeriodicityInterface $periodicity, EntryInterface $entryToSkip): void
    {
        foreach ($this->entryRepository->findByPeriodicity($periodicity) as $entry) {
            if ($entry->getId() !== $entryToSkip->getId()) {
                $this->remove($entry);
            }
        }
    }
}
