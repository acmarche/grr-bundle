<?php

namespace Grr\GrrBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Grr\Core\Area\Entity\AreaTrait;
use Grr\Core\Contrat\Entity\AreaInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Area.
 *
 * @ORM\Table(name="area")
 * @ORM\Entity(repositoryClass="Grr\GrrBundle\Area\Repository\AreaRepository")
 * @ApiResource
 */
class Area implements AreaInterface
{
    use AreaTrait;
}
