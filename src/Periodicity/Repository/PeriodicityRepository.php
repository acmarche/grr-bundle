<?php

namespace Grr\GrrBundle\Periodicity\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Grr\Core\Contrat\Repository\PeriodicityRepositoryInterface;
use Grr\Core\Doctrine\OrmCrudTrait;
use Grr\GrrBundle\Entity\Periodicity;

/**
 * @method Periodicity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Periodicity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Periodicity[]    findAll()
 * @method Periodicity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PeriodicityRepository extends ServiceEntityRepository implements PeriodicityRepositoryInterface
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Periodicity::class);
    }
}
