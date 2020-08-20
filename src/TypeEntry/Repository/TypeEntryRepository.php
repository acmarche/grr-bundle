<?php

namespace Grr\GrrBundle\TypeEntry\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeEntry::class);
    }
}
