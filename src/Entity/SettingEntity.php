<?php

namespace Grr\GrrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Grr\Core\Contrat\Entity\SettingEntityInterface;
use Grr\Core\Setting\Entity\SettingTrait;
use Grr\GrrBundle\Setting\Repository\SettingRepository;

#[ORM\Table(name: 'setting')]
#[ORM\Entity(repositoryClass: SettingRepository::class)]
class SettingEntity implements SettingEntityInterface
{
    use SettingTrait;
}
