<?php

namespace Grr\GrrBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Id;
use Grr\Core\Area\Entity\AreaTrait;
use Grr\Core\Contrat\Entity\AreaInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Area.
 *
 * @ORM\Table(name="area")
 * @ORM\Entity(repositoryClass="Grr\GrrBundle\Area\Repository\AreaRepository")
 * @ApiResource(normalizationContext={"groups"="area:read"}, denormalizationContext={"groups"="area:write"}, itemOperations={"get"})
 * @ApiFilter(SearchFilter::class, properties={"nom"="partial", "id"="exact"})
 */
class Area implements AreaInterface
{
    use AreaTrait;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=80, nullable=false)
     * @Groups("area:read")
     */
    private ?string $name;
}
