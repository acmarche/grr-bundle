<?php

namespace Grr\GrrBundle\Setting;

use Grr\GrrBundle\Area\Repository\AreaRepository;
use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\Room;
use Grr\GrrBundle\Room\Repository\RoomRepository;

class SettingsProvider
{
    /**
     * @var AreaRepository
     */
    private $areaRepository;

    public function __construct(\Grr\Core\Contrat\Repository\AreaRepositoryInterface $areaRepository)
    {
        $this->areaRepository = $areaRepository;
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
    }
}
