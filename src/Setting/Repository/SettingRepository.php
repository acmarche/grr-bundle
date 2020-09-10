<?php

namespace Grr\GrrBundle\Setting\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Grr\Core\Contrat\Repository\SettingRepositoryInterface;
use Grr\Core\Setting\SettingConstants;
use Grr\GrrBundle\Entity\Setting;

/**
 * @method Setting|null find($id, $lockMode = null, $lockVersion = null)
 * @method Setting|null findOneBy(array $criteria, array $orderBy = null)
 * @method Setting[]    findAll()
 * @method Setting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SettingRepository extends ServiceEntityRepository implements SettingRepositoryInterface
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Setting::class);
    }

    public function getValueByName(string $name): ?string
    {
        try {
            $setting = $this->createQueryBuilder('setting')
                ->andWhere('setting.name = :name')
                ->setParameter('name', $name)
                ->getQuery()
                ->getOneOrNullResult();
            if ($setting) {
                return $setting->getValue();
            }
        } catch (NonUniqueResultException $e) {
        }

        return null;
    }

    /**
     * @return mixed[]
     */
    public function load(): array
    {
        $data = [];

        foreach ($this->findAll() as $setting) {
            $value = $setting->getValue();
            if (in_array(
                $setting->getName(),
                [SettingConstants::WEBMASTER_EMAIL, SettingConstants::TECHNICAL_SUPPORT_EMAIL],
                true
            )) {
                $value = unserialize($value);
            }
            $data[$setting->getName()] = $value;
        }

        /*
         * Pour bug js form edit
         */
        if (!isset($data[SettingConstants::WEBMASTER_EMAIL]) || 0 === count($data[SettingConstants::WEBMASTER_EMAIL])) {
            $data[SettingConstants::WEBMASTER_EMAIL] = [''];
        }

        if (!isset($data[SettingConstants::TECHNICAL_SUPPORT_EMAIL]) || 0 === count(
                $data[SettingConstants::TECHNICAL_SUPPORT_EMAIL]
            )) {
            $data[SettingConstants::TECHNICAL_SUPPORT_EMAIL] = [''];
        }

        return $data;
    }
}
