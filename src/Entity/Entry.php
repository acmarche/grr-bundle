<?php

namespace Grr\GrrBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\ORM\Mapping as ORM;
use Grr\Core\Contrat\Entity\EntryInterface;
use Grr\Core\Contrat\Entity\RoomInterface;
use Grr\Core\Entry\Entity\EntryTrait;
use Grr\GrrBundle\Entry\Validator as GrrEntryAssert;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="entry")
 * @ORM\Entity(repositoryClass="Grr\GrrBundle\Entry\Repository\EntryRepository")
 * @GrrEntryAssert\BusyRoom
 * @GrrEntryAssert\AreaTimeSlot
 *
 * @ApiResource(
 *     normalizationContext={"groups"={"entry:read"}},
 *     denormalizationContext={"groups"={"entry:write"}},
 *     itemOperations={"get"}
 * )
 * @ApiFilter(SearchFilter::class, properties={"nom": "partial", "id": "exact", "room": "exact"})
 */
class Entry implements EntryInterface, TimestampableInterface
{
    use EntryTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups("entry:read")
     *
     * @var int
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=80, nullable=false)
     * @Groups("entry:read")
     */
    private $name;

    /**
     * @var RoomInterface
     * @ORM\ManyToOne(targetEntity="Grr\Core\Contrat\Entity\RoomInterface", inversedBy="entries")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("entry:read")
     */
    private $room;
}
