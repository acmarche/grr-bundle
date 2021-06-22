<?php

namespace Grr\GrrBundle\Authorization\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Grr\Core\Contrat\Entity\AreaInterface;
use Grr\Core\Contrat\Entity\RoomInterface;
use Grr\Core\Contrat\Entity\Security\AuthorizationInterface;
use Grr\Core\Contrat\Entity\Security\UserInterface;
use Grr\Core\Contrat\Repository\Security\AuthorizationRepositoryInterface;
use Grr\GrrBundle\Entity\Room;
use Grr\GrrBundle\Entity\Security\Authorization;

/**
 * @method Authorization|null find($id, $lockMode = null, $lockVersion = null)
 * @method Authorization|null findOneBy(array $criteria, array $orderBy = null)
 * @method Authorization[]    findAll()
 * @method Authorization[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorizationRepository extends ServiceEntityRepository implements AuthorizationRepositoryInterface
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Authorization::class);
    }

    /**
     * Pour montrer les droits par area.
     *
     * @return Authorization[]
     *
     * @throws Exception
     */
    public function findByArea(AreaInterface $area): array
    {
        return $this->findByUserAndArea(null, $area);
    }

    /**
     * Pour montrer les droits par user.
     *
     * @return Authorization[]
     *
     * @throws Exception
     */
    public function findByUser(UserInterface $user): array
    {
        return $this->findByUserAndArea($user, null);
    }

    /**
     * getRoomsUserCanAdd.
     *
     * @param UserInterface $user
     *
     * @return Authorization[]
     *
     * @throws Exception
     */
    public function findByUserAndArea(?UserInterface $user, ?AreaInterface $area): array
    {
        if (!$user && !$area) {
            throw new Exception('At least one parameter is needed');
        }

        $queryBuilder = $this->createQueryBuilder('authorization');

        if (null !== $user) {
            $this->setCriteriaUser($queryBuilder, $user);
        }

        if (null !== $area) {
            $this->setCriteriaArea($queryBuilder, $area);
        }

        return $queryBuilder
            ->addOrderBy('authorization.user', 'ASC')
            ->orderBy('authorization.room', 'ASC')
            ->orderBy('authorization.area', 'ASC')
            ->getQuery()
            ->getResult();
    }

    protected function setCriteriaUser(QueryBuilder $queryBuilder, UserInterface $user): void
    {
        $queryBuilder->andWhere('authorization.user = :user')
            ->setParameter('user', $user);
    }

    protected function setCriteriaArea(QueryBuilder $queryBuilder, AreaInterface $area): void
    {
        $repository = $this->getEntityManager()->getRepository(Room::class);
        $rooms = $repository->findByArea($area);
        $queryBuilder->andWhere('authorization.area = :area')
            ->setParameter('area', $area);

        $queryBuilder->orWhere('authorization.room IN (:rooms)')
            ->setParameter('rooms', $rooms);
    }

    /**
     * Pour montrer les droits par room.
     *
     * @return Authorization[]
     */
    public function findByRoom(RoomInterface $room): array
    {
        return $this->createQueryBuilder('authorization')
            ->andWhere('authorization.room = :room')
            ->setParameter('room', $room)
            ->addOrderBy('authorization.user', 'ASC')
            ->orderBy('authorization.area', 'ASC')
            ->orderBy('authorization.room', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Utilise dans migration checker.
     *
     * @return Authorization[]
     */
    public function findByUserAndAreaNotNull(UserInterface $user, bool $isAreaAdministrator): array
    {
        $queryBuilder = $this->createQueryBuilder('authorization');

        $this->setCriteriaUser($queryBuilder, $user);

        $queryBuilder->andWhere('authorization.area IS NOT NULL');

        $queryBuilder->andWhere('authorization.isAreaAdministrator = :bool')
            ->setParameter('bool', $isAreaAdministrator);

        return $queryBuilder
            ->addOrderBy('authorization.user', 'ASC')
            ->orderBy('authorization.area', 'ASC')
            ->orderBy('authorization.room', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Utilise dans migration checker.
     *
     * @throws NonUniqueResultException
     */
    public function findOneByUserAndRoom(UserInterface $user, RoomInterface $room): Authorization
    {
        $queryBuilder = $this->createQueryBuilder('authorization');

        $this->setCriteriaUser($queryBuilder, $user);

        $queryBuilder->andWhere('authorization.room = :room')
            ->setParameter('room', $room);

        return $queryBuilder->orderBy('authorization.user', 'ASC')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return AuthorizationInterface[]
     */
    public function findByAreaOrRoom(AreaInterface $area, RoomInterface $room): array
    {
        return $this->createQueryBuilder('authorization')
            ->orWhere('authorization.room = :room')
            ->setParameter('room', $room)
            ->orWhere('authorization.area = :area')
            ->setParameter('area', $area)
            ->getQuery()->getResult();
    }
}
