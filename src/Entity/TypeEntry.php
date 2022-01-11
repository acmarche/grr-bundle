<?php

namespace Grr\GrrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Grr\Core\Contrat\Entity\TypeEntryInterface;
use Grr\Core\TypeEntry\Entity\TypeEntryTrait;
use Grr\GrrBundle\TypeEntry\Repository\TypeEntryRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'entry_type')]
#[ORM\UniqueConstraint(columns: ['letter'])]
#[ORM\Entity(repositoryClass: TypeEntryRepository::class)]
#[UniqueEntity(fields: ['letter'], message: 'constraint.entryType.alreadyUse')]
class TypeEntry implements TypeEntryInterface
{
    use TypeEntryTrait;
}
