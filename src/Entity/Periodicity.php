<?php

namespace Grr\GrrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Grr\Core\Contrat\Entity\PeriodicityInterface;
use Grr\Core\Periodicity\Entity\PeriodicityTrait;
use Grr\GrrBundle\Periodicity\Validator as GrrAssert;

/**
 * @ORM\Table(name="periodicity")
 * @ORM\Entity(repositoryClass="Grr\GrrBundle\Periodicity\Repository\PeriodicityRepository")
 * @GrrAssert\Periodicity
 * @GrrAssert\PeriodicityEveryDay
 * @GrrAssert\PeriodicityEveryMonth
 * @GrrAssert\PeriodicityEveryYear
 * @GrrAssert\PeriodicityEveryWeek
 */
class Periodicity implements PeriodicityInterface
{
    use PeriodicityTrait;
}
