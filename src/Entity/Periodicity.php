<?php

namespace Grr\GrrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Grr\Core\Entity\PeriodicityInterface;
use Grr\Core\Entity\PeriodicityTrait;

/**
 *
 * @ORM\Table(name="periodicity")
 * @ORM\Entity(repositoryClass="Grr\GrrBundle\Repository\PeriodicityRepository")
 * AppAssertPeriodicity\Periodicity
 * AppAssertPeriodicity\PeriodicityEveryDay
 * AppAssertPeriodicity\PeriodicityEveryMonth
 * AppAssertPeriodicity\PeriodicityEveryYear
 * AppAssertPeriodicity\PeriodicityEveryWeek
 */
class Periodicity implements PeriodicityInterface
{
    use PeriodicityTrait;
}
