<?php

namespace Grr\GrrBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\ORM\Mapping as ORM;
use Grr\Core\Contrat\Entity\EntryInterface;
use Grr\Core\Contrat\Entity\RoomInterface;
use Grr\Core\Entry\Entity\EntryTrait;
use Grr\GrrBundle\Entry\Repository\EntryRepository;
use Grr\GrrBundle\Entry\Validator as GrrEntryAssert;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @GrrEntryAssert\BusyRoom
 * @GrrEntryAssert\AreaTimeSlot
 * @ApiResource(normalizationContext={"groups"="entry:read"}, denormalizationContext={"groups"="entry:write"}, itemOperations={"get"})
 * @ApiFilter(SearchFilter::class, properties={"nom"="partial", "id"="exact", "room"="exact"})
 */
#[ORM\Table(name: 'entry')]
#[ORM\Entity(repositoryClass: EntryRepository::class)]
class Entry implements EntryInterface, TimestampableInterface
{
    use EntryTrait;
    #[Assert\NotBlank]
    #[ORM\Column(type: 'string', length: 80, nullable: false)]
    #[Groups(groups: 'entry:read')]
    private ?string $name = null;
    #[ORM\ManyToOne(targetEntity: RoomInterface::class, inversedBy: 'entries')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(groups: 'entry:read')]
    private ?RoomInterface $room = null;
}
