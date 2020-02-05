<?php

namespace Grr\GrrBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Grr\Core\Contrat\Entity\AreaInterface;
use Grr\Core\Entity\AreaTrait;

/**
 * Area.
 *
 * @ORM\Table(name="area")
 * @ORM\Entity(repositoryClass="Grr\GrrBundle\Repository\AreaRepository")
 * @ApiResource
 */
Class Area implements AreaInterface
{
    use AreaTrait;
}
