<?php

namespace Grr\GrrBundle\User\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Grr\Core\Contrat\Repository\Security\UserRepositoryInterface;
use Grr\GrrBundle\Entity\Security\User;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, User::class);
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('user')
            ->orderBy('user.name', 'ASC');
    }

    /**
     * @param string $username
     * @return User|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadByUserNameOrEmail(string $username): User
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
        $queryBuilder = $this->createQueryBuilder('user')
            ->orderBy('user.name', 'ASC');

        $name = $args['name'] ?? null;
        if ($name) {
            $queryBuilder->andWhere('user.email LIKE :name OR user.name LIKE :name OR user.username LIKE :name')
                ->setParameter('name', '%'.$name.'%');
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function listReservedFor(): array
    {
        $queryBuilder = $this->createQueryBuilder('user')
            ->orderBy('user.name', 'ASC');
        $users = [];
        foreach ($queryBuilder->getQuery()->getResult() as $user) {
            $users[$user->getName()] = $user->getUsername();
        }

        return $users;
    }
}
