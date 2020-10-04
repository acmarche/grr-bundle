<?php

namespace Grr\GrrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Grr\Core\Contrat\Entity\AreaInterface;
use Grr\Core\Contrat\Entity\RoomInterface;
use Grr\Core\Room\Entity\RoomTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * Room.
 *
 * @ORM\Table(name="room")
 * @ORM\Entity(repositoryClass="Grr\GrrBundle\Room\Repository\RoomRepository")
 * @ApiResource(
 *     normalizationContext={"groups"={"room:read"}},
 *     denormalizationContext={"groups"={"room:write"}},
 *     itemOperations={"get"}
 * )
 * @ApiFilter(SearchFilter::class, properties={"nom": "partial", "id": "exact", "area": "exact"})
 */
class Room implements RoomInterface
{
    use RoomTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups("room:read")
     *
     * @var int
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=80, nullable=false)
     * @Groups("room:read")
     */
    private $name;

    /**
     * @var AreaInterface
     * @ORM\ManyToOne(targetEntity="Grr\Core\Contrat\Entity\AreaInterface", inversedBy="rooms")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("room:read")
     */
    private $area;
}
