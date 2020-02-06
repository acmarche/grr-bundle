<?php

namespace Grr\GrrBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Grr\Core\Contrat\Entity\EntryTypeInterface;
use Grr\Core\EntryType\Entity\EntryTypeTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="entry_type", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"letter"})
 * })
 * @ORM\Entity(repositoryClass="Grr\GrrBundle\Repository\EntryTypeRepository")
 * @UniqueEntity(fields={"letter"}, message="entry_type.constraint.already_use")
 * @ApiResource
 */
class EntryType implements EntryTypeInterface
{
    use EntryTypeTrait;
}
