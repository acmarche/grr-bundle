<?php

namespace Grr\GrrBundle\Repository\Security;

use Grr\GrrBundle\Entity\Security\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Grr\Core\Repository\Security\UserRepositoryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('user')
            ->orderBy('user.name', 'ASC');
    }

    public function loadByUserNameOrEmail(string $username)
    {
        return $this->createQueryBuilder('user')
            ->andWhere('user.email = :username')
            ->orWhere('user.username = :username')
            ->setParameter('username', $username)
            ->orderBy('user.name', 'ASC')
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @return User[]
     */
    public function search(array $args): array
    {
        $qb = $this->createQueryBuilder('user')
            ->orderBy('user.name', 'ASC');

        $name = $args['name'] ?? null;
        if ($name) {
            $qb->andWhere('user.email LIKE :name OR user.name LIKE :name OR user.username LIKE :name')
                ->setParameter('name', '%'.$name.'%');
        }

        return $qb->getQuery()->getResult();
    }
}
