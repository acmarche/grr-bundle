<?php

namespace Grr\GrrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Grr\Core\Contrat\Entity\RoomInterface;
use Grr\Core\Room\Entity\RoomTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Room.
 *
 * @ORM\Table(name="room")
 * @ORM\Entity(repositoryClass="Grr\GrrBundle\Room\Repository\RoomRepository")
 */
class Room implements RoomInterface
{
    use RoomTrait;
}
