<?php

namespace Grr\GrrBundle\Navigation;

use Exception;
use Grr\Core\Contrat\Entity\AreaInterface;
use Grr\Core\Contrat\Entity\RoomInterface;
use Grr\Core\Contrat\Entity\Security\UserInterface;
use Grr\Core\Contrat\Repository\AreaRepositoryInterface;
use Grr\Core\Contrat\Repository\RoomRepositoryInterface;
use Grr\Core\Setting\Repository\SettingProvider;
use Grr\GrrBundle\Entity\Area;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;

class RessourceSelectedHelper
{
    public const AREA_DEFAULT_SESSION = 'area_seleted';
    public const ROOM_DEFAULT_SESSION = 'room_seleted';
    private SessionInterface $session;

    public function __construct(
        private RequestStack $requestStack,
        private Security $security,
        private SettingProvider $settingProvider,
        private AreaRepositoryInterface $areaRepository,
        private RoomRepositoryInterface $roomRepository
    ) {

    }

    /**
     * @throws Exception
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

        $area = $this->areaRepository->findOneBy([], [
            'id' => 'ASC',
        ]);
        if (!$area instanceof Area) {
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

    private function setSession()
    {
        if ($session = $this->requestStack->getSession()) {
            $this->session = $session;
        }
    }
}
