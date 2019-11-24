<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 1/03/19
 * Time: 17:42.
 */

namespace Grr\GrrBundle\Area;

use Grr\GrrBundle\Entity\Area;

class AreaFactory
{
    public function createNew(): Area
    {
        return new Area();
    }
}
