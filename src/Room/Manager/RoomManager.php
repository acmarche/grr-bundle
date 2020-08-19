<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 1/03/19
 * Time: 19:59.
 */

namespace Grr\GrrBundle\Room\Manager;

use Grr\GrrBundle\Entity\Room;
use Grr\GrrBundle\Manager\BaseManager;

class RoomManager extends BaseManager
{
    public function removeEntries(Room $room): void
    {
        foreach ($room->getEntries() as $entry) {
            $this->entityManager->remove($entry);
        }
    }
}
