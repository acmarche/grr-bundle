<?php

namespace Grr\GrrBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Grr\Core\Contrat\Entity\SettingInterface;
use Grr\Core\Entity\SettingTrait;

/**
 * Setting.
 *
 * @ORM\Table(name="setting")
 * @ORM\Entity(repositoryClass="Grr\GrrBundle\Repository\SettingRepository")
 * @ApiResource
 */
class Setting implements SettingInterface
{
    use SettingTrait;
}
