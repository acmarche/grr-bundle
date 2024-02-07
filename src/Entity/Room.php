<?php

namespace Grr\GrrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Grr\Core\Contrat\Entity\AreaInterface;
use Grr\Core\Contrat\Entity\RoomInterface;
use Grr\Core\Room\Entity\RoomTrait;
use Grr\GrrBundle\Room\Repository\RoomRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'room')]
#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room implements RoomInterface
{
    use RoomTrait;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'string', length: 80, nullable: false)]
    #[Groups(groups: 'room:read')]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: AreaInterface::class, inversedBy: 'rooms')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(groups: 'room:read')]
    private ?AreaInterface $area = null;
}
