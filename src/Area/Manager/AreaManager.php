<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 1/03/19
 * Time: 19:59.
 */

namespace Grr\GrrBundle\Area\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Grr\GrrBundle\Entity\Area;
use Grr\Core\Manager\BaseManager;
use Grr\GrrBundle\Room\Manager\RoomManager;

class AreaManager extends BaseManager
{
    /**
     * @var RoomManager
     */
    private $roomManager;

    public function __construct(EntityManagerInterface $entityManager, RoomManager $roomManager)
    {
        parent::__construct($entityManager);
        $this->entityManager = $entityManager;
        $this->roomManager = $roomManager;
    }

    public function removeRooms(Area $area): void
    {
        foreach ($area->getRooms() as $room) {
            $this->roomManager->remove($room);
        }
        $this->flush();
    }
}
