<?php

namespace Grr\GrrBundle\Booking\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Grr\Core\Doctrine\OrmCrudTrait;
use Grr\GrrBundle\Entity\Booking;

/**
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    /**
     * @return Booking[] Returns an array of Booking objects
     */
    public function findNotDone()
    {
        return $this->createQueryBuilder('booking')
            ->andWhere('booking.done = 0')
            ->orderBy('booking.jour', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
