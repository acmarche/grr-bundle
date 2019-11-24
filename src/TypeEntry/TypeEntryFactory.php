<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 1/03/19
 * Time: 17:42.
 */

namespace Grr\GrrBundle\TypeEntry;

use Grr\GrrBundle\Entity\EntryType;

class TypeEntryFactory
{
    public function createNew(): EntryType
    {
        return new EntryType();
    }
}
