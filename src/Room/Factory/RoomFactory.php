<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 1/03/19
 * Time: 17:42.
 */

namespace Grr\GrrBundle\Room\Factory;

use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\Room;

class RoomFactory
{
    public function createNew(Area $area): Room
    {
        return new Room($area);
    }
}
