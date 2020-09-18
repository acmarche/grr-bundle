<?php

namespace Grr\GrrBundle\Setting\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Grr\Core\Contrat\Repository\SettingRepositoryInterface;
use Grr\GrrBundle\Entity\SettingEntity;

/**
 * @method SettingEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method SettingEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method SettingEntity[]    findAll()
 * @method SettingEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SettingRepository extends ServiceEntityRepository implements SettingRepositoryInterface
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, SettingEntity::class);
    }

    public function getValueByName(string $name): ?string
    {
        $setting = $this->createQueryBuilder('setting')
            ->andWhere('setting.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();

        if ($setting) {
            return (string) $setting->getValue();
        }

        return null;
    }

    public function getSettingByName(string $name): ?SettingEntity
    {
        return $this->createQueryBuilder('setting')
            ->andWhere('setting.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
