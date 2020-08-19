<?php

namespace Grr\GrrBundle\Periodicity\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Grr\Core\Contrat\Repository\PeriodicityRepositoryInterface;
use Grr\GrrBundle\Entity\Periodicity;

/**
 * @method Periodicity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Periodicity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Periodicity[]    findAll()
 * @method Periodicity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PeriodicityRepository extends ServiceEntityRepository implements PeriodicityRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Periodicity::class);
    }
}
