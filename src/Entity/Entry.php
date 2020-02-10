<?php

namespace Grr\GrrBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Grr\Core\Contrat\Entity\EntryInterface;
use Grr\Core\Entry\Entity\EntryTrait;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Grr\GrrBundle\Validator as GrrAssert;

/**
 * @ORM\Table(name="entry")
 * @ORM\Entity(repositoryClass="Grr\GrrBundle\Repository\EntryRepository")
 * @GrrAssert\Entry\BusyRoom
 * @GrrAssert\Entry\AreaTimeSlot
 * @ApiResource
 */
class Entry implements EntryInterface, TimestampableInterface
{
    use EntryTrait;
}
