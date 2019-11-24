<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 26/03/19
 * Time: 11:12.
 */

namespace Grr\GrrBundle\Navigation;

use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\Room;

class MenuSelect
{
    /**
     * @var Area
     */
    private $area;

    /**
     * @var Room|null
     */
    private $room;

    public function getArea(): Area
    {
        return $this->area;
    }

    public function setArea(Area $area): void
    {
        $this->area = $area;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(Room $room = null): void
    {
        $this->room = $room;
    }
}
