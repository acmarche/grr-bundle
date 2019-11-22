<?php

namespace Grr\GrrBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Grr\Core\Repository\EntryTypeRepositoryInterface;
use Grr\GrrBundle\Entity\EntryType;

/**
 * @method EntryType|null find($id, $lockMode = null, $lockVersion = null)
 * @method EntryType|null findOneBy(array $criteria, array $orderBy = null)
 * @method EntryType[]    findAll()
 * @method EntryType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EntryTypeRepository extends ServiceEntityRepository implements EntryTypeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EntryType::class);
    }
}
