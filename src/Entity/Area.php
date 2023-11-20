<?php

namespace Grr\GrrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Grr\Core\Area\Entity\AreaTrait;
use Grr\Core\Contrat\Entity\AreaInterface;
use Grr\GrrBundle\Area\Repository\AreaRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'area')]
#[ORM\Entity(repositoryClass: AreaRepository::class)]
class Area implements AreaInterface
{
    use AreaTrait;
    #[Assert\NotBlank]
    #[ORM\Column(type: 'string', length: 80, nullable: false)]
    #[Groups(groups: 'area:read')]
    private ?string $name = null;
}
