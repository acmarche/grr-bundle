<?php

namespace Grr\GrrBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Grr\Core\Entity\EntryInterface;
use Grr\Core\Entity\EntryTrait;

/**
 *
 * @ORM\Table(name="entry")
 * @ORM\Entity(repositoryClass="Grr\GrrBundle\Repository\EntryRepository")
 * AppAssertEntry\BusyRoom
 * AppAssertEntry\AreaTimeSlot
 * @ApiResource
 */
class Entry implements EntryInterface
{
    use EntryTrait;
}
