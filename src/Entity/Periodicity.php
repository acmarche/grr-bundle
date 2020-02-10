<?php

namespace Grr\GrrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Grr\Core\Contrat\Entity\PeriodicityInterface;
use Grr\Core\Periodicity\Entity\PeriodicityTrait;
use Grr\GrrBundle\Validator as GrrAssert;

/**
 * @ORM\Table(name="periodicity")
 * @ORM\Entity(repositoryClass="Grr\GrrBundle\Repository\PeriodicityRepository")
 * @GrrAssert\Periodicity\Periodicity
 * @GrrAssert\Periodicity\PeriodicityEveryDay
 * @GrrAssert\Periodicity\PeriodicityEveryMonth
 * @GrrAssert\Periodicity\PeriodicityEveryYear
 * @GrrAssert\Periodicity\PeriodicityEveryWeek
 */
class Periodicity implements PeriodicityInterface
{
    use PeriodicityTrait;
}
