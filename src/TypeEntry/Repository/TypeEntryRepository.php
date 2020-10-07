<?php

namespace Grr\GrrBundle\TypeEntry\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Grr\Core\Contrat\Entity\AreaInterface;
use Grr\Core\Contrat\Repository\TypeEntryRepositoryInterface;
use Grr\GrrBundle\Entity\TypeEntry;

/**
 * @method TypeEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeEntry[]    findAll()
 * @method TypeEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeEntryRepository extends ServiceEntityRepository implements TypeEntryRepositoryInterface
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, TypeEntry::class);
    }

    /**
     * @param AreaInterface|null $area
     * @return TypeEntry[]
     */
    public function findByArea(?AreaInterface $area): array
    {
        //todo filter area
        $queryBuilder = $this->createQueryBuilder('type_entry');

        return $this->findAll();
    }
}
