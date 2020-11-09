<?php

namespace Grr\GrrBundle\Preference\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Grr\Core\Contrat\Entity\Security\UserInterface;
use Grr\GrrBundle\Entity\Preference\EmailPreference;

/**
 * @method EmailPreference|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmailPreference|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmailPreference[]    findAll()
 * @method EmailPreference[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailPreferenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, EmailPreference::class);
    }

    public function findOneByUser(UserInterface $user): ?EmailPreference
    {
        return $this->createQueryBuilder('noti')
            ->andWhere('noti.user = :user')
            ->setParameter('user', $user)
            ->getQuery()->getOneOrNullResult();
    }

    public function findByUser(UserInterface $user, string $action): ?EmailPreference
    {
        $preference = $this->findOneByUser($user);
    }
}
