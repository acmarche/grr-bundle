<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 1/03/19
 * Time: 19:59.
 */

namespace Grr\GrrBundle\Manager;

use Grr\GrrBundle\Entity\Area;
use Doctrine\ORM\EntityManagerInterface;

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
