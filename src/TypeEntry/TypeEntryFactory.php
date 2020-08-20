<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 1/03/19
 * Time: 17:42.
 */

namespace Grr\GrrBundle\TypeEntry;

use Grr\GrrBundle\Entity\TypeEntry;

class TypeEntryFactory
{
    public function createNew(): TypeEntry
    {
        return new TypeEntry();
    }
}
