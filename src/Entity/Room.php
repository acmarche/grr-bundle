<?php

namespace Grr\GrrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Grr\Core\Entity\AreaInterface;
use Grr\Core\Entity\RoomInterface;
use Grr\Core\Entity\RoomTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Room.
 *
 * @ORM\Table(name="room")
 * @ORM\Entity(repositoryClass="Grr\GrrBundle\Repository\RoomRepository")
 */
class Room implements RoomInterface
{
    use RoomTrait;
}
