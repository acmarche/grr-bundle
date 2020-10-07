<?php

namespace Grr\GrrBundle\Navigation;

use Exception;
use Grr\Core\Contrat\Entity\AreaInterface;
use Grr\Core\Contrat\Entity\RoomInterface;
use Grr\Core\Contrat\Entity\Security\UserInterface;
use Grr\Core\Contrat\Repository\AreaRepositoryInterface;
use Grr\Core\Contrat\Repository\RoomRepositoryInterface;
use Grr\Core\Setting\Repository\SettingProvider;
use Grr\GrrBundle\Area\Repository\AreaRepository;
use Grr\GrrBundle\Room\Repository\RoomRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Class RessourceSelectedHelper.
 */
class RessourceSelectedHelper
{
    const AREA_DEFAULT_SESSION = 'area_seleted';
    const ROOM_DEFAULT_SESSION = 'room_seleted';

    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var Security
     */
    private $security;
    /**
     * @var AreaRepository
     */
    private $areaRepository;
    /**
     * @var RoomRepository
     */
    private $roomRepository;

    public function __construct(
        SessionInterface $session,
        Security $security,
        SettingProvider $settingProvider,
        AreaRepositoryInterface $areaRepository,
        RoomRepositoryInterface $roomRepository
    ) {
        $this->session = $session;
        $this->security = $security;
        $this->settingProvider = $settingProvider;
        $this->areaRepository = $areaRepository;
        $this->roomRepository = $roomRepository;
    }

    /**
     * @throws \Exception
     */
    public function getArea(): AreaInterface
    {
        if ($this->session->has(self::AREA_DEFAULT_SESSION)) {
            $areaId = $this->session->get(self::AREA_DEFAULT_SESSION);

            if (null !== ($area = $this->areaRepository->find($areaId))) {
                return $area;
            }
        }

        /**
         * @var UserInterface $user
         */
        $user = $this->security->getUser();
        if (null !== $user && null !== ($area = $user->getArea())) {
            return $area;
        }

        if (null !== ($area = $this->settingProvider->getDefaultArea())) {
            return $area;
        }

        $area = $this->areaRepository->findOneBy([], ['id' => 'ASC']);
        if (null === $area) {
            throw new Exception(
                'No area in database, populate database with this command: php bin/console grr:install-data'
            );
        }

        return $area;
    }

    /**
     * -1 = force all ressource.
     */
    public function getRoom(): ?RoomInterface
    {
        if ($this->session->has(self::ROOM_DEFAULT_SESSION)) {
            $roomId = $this->session->get(self::ROOM_DEFAULT_SESSION);
            if (-1 === $roomId) {
                return null;
            }
            if ($roomId) {
                return $this->roomRepository->find($roomId);
            }
        }

        /**
         * @var UserInterface $user
         */
        $user = $this->security->getUser();
        if (null !== $user && null !== ($room = $user->getRoom())) {
            return $room;
        }

        if (null !== ($room = $this->settingProvider->getDefaulRoom())) {
            return $room;
        }

        return null;
    }

    public function setSelected(int $area, int $room = null): void
    {
        $this->session->set(self::AREA_DEFAULT_SESSION, $area);
        if ($room) {
            $this->session->set(self::ROOM_DEFAULT_SESSION, $room);

            return;
        }

        $this->session->remove(self::ROOM_DEFAULT_SESSION);
    }
}
