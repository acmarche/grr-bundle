<?php

namespace Grr\GrrBundle\Setting;

use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\Room;
use Grr\GrrBundle\Repository\AreaRepository;
use Grr\GrrBundle\Repository\RoomRepository;

class SettingsProvider
{
    /**
     * @var AreaRepository
     */
    private $areaRepository;
    /**
     * @var RoomRepository
     */
    private $roomRepository;

    public function __construct(AreaRepository $areaRepository, RoomRepository $roomRepository)
    {
        $this->areaRepository = $areaRepository;
        $this->roomRepository = $roomRepository;
    }

    /**
     * @todo
     */
    public function getDefaultArea(): ?Area
    {
        return $this->areaRepository->findOneBy([], ['id' => 'ASC']);
    }

    /**
     * @todo default room
     */
    public function getDefaulRoom(): ?Room
    {
        return null;

        return $this->roomRepository->findOneBy([], ['id' => 'ASC']);
    }
}
