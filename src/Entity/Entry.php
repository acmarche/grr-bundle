<?php

namespace Grr\GrrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Grr\Core\Contrat\Entity\EntryInterface;
use Grr\Core\Contrat\Entity\RoomInterface;
use Grr\Core\Entry\Entity\EntryTrait;
use Grr\GrrBundle\Entry\Repository\EntryRepository;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

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
