<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 1/03/19
 * Time: 17:42.
 */

namespace Grr\GrrBundle\Periodicity\Factory;

use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Entity\Periodicity;

class PeriodicityFactory
{
    public function createNew(Entry $entry): Periodicity
    {
        return new Periodicity($entry);
    }
}
