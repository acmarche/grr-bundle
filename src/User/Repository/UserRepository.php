<?php

namespace Grr\GrrBundle\User\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Grr\Core\Contrat\Repository\Security\UserRepositoryInterface;
use Grr\Core\Doctrine\OrmCrudTrait;
use Grr\Core\Security\SecurityRole;
use Grr\GrrBundle\Entity\Security\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserRepositoryInterface
{
    use OrmCrudTrait;

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
     * @see UserProviderListener::checkPassport
     *
     * @return int|mixed|string|null
     *
     * @throws NonUniqueResultException
     */
    public function loadUserByIdentifier(string $username)
    {
        return $this->createQueryBuilder('user')
            ->andWhere('user.email = :username OR user.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (! $user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function loadByUserNameOrEmail(string $username): ?User
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

    /**
     * @return UserRepositoryInterface[]
     */
    public function getGrrAdministrators(): array
    {
        return $this->createQueryBuilder('user')
            ->andWhere('user.roles LIKE :role ')
            ->setParameter('role', '%'.SecurityRole::ROLE_GRR_ADMINISTRATOR.'%')
            ->getQuery()->getResult();
    }
}
