<?php

namespace Grr\GrrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Grr\Core\Contrat\Entity\SettingEntityInterface;
use Grr\Core\Setting\Entity\SettingTrait;

/**
 * Setting.
 *
 * @ORM\Table(name="setting")
 * @ORM\Entity(repositoryClass="Grr\GrrBundle\Setting\Repository\SettingRepository")
 */
class SettingEntity implements SettingEntityInterface
{
    use SettingTrait;
}
