<?php

namespace Grr\GrrBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Grr\Core\Contrat\Entity\TypeEntryInterface;
use Grr\Core\TypeEntry\Entity\TypeEntryTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="entry_type", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"letter"})
 * })
 * @ORM\Entity(repositoryClass="Grr\GrrBundle\TypeEntry\Repository\TypeEntryRepository")
 * @UniqueEntity(fields={"letter"}, message="constraint.entryType.alreadyUse")
 * @ApiResource
 */
class TypeEntry implements TypeEntryInterface
{
    use TypeEntryTrait;
}
