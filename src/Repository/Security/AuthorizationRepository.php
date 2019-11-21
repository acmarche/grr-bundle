<?php

namespace Grr\GrrBundle\Repository\Security;

use App\Grr\GrrBundle\src\Entity\Security\Authorization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Grr\Core\Repository\Security\AuthorizationRepositoryInterface;
use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\Room;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Authorization|null find($id, $lockMode = null, $lockVersion = null)
 * @method Authorization|null findOneBy(array $criteria, array $orderBy = null)
 * @method Authorization[]    findAll()
 * @method Authorization[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorizationRepository extends ServiceEntityRepository implements AuthorizationRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Authorization::class);
    }

    /**
     * Pour montrer les droits par area.
     *
     * @return Authorization[]
     *
     * @throws \Exception
     */
    public function findByArea(Area $area): array
    {
        return $this->findByUserAndArea(null, $area);
    }

    /**
     * Pour montrer les droits par user.
     *
     * @return Authorization[]
     *
     * @throws \Exception
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
     * @throws \Exception
     */
    public function findByUserAndArea(?UserInterface $user, ?Area $area): array
    {
        if (!$user && !$area) {
            throw new Exception('At least one parameter is needed');
        }

        $queryBuilder = $this->createQueryBuilder('authorization');

        if ($user !== null) {
            $this->setCriteriaUser($queryBuilder, $user);
        }

        if ($area !== null) {
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

    protected function setCriteriaArea(QueryBuilder $queryBuilder, Area $area): void
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
    public function findByRoom(Room $room): array
    {
        $queryBuilder = $this->createQueryBuilder('authorization')
            ->andWhere('authorization.room = :room')
            ->setParameter('room', $room);

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
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByUserAndRoom(UserInterface $user, Room $room): Authorization
    {
        $queryBuilder = $this->createQueryBuilder('authorization');

        $this->setCriteriaUser($queryBuilder, $user);

        $queryBuilder->andWhere('authorization.room = :room')
            ->setParameter('room', $room);

        return $queryBuilder->orderBy('authorization.user', 'ASC')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
