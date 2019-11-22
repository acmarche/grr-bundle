<?php

namespace Grr\GrrBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Grr\Core\Entity\AreaInterface;
use Grr\Core\Entity\AreaTrait;
use Grr\Core\Entity\RoomFieldTrait;
use Symfony\Component\Validator\Constraints as Assert;

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
