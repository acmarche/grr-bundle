<?php
/**
 * This file is part of GrrSf application.
 *
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 26/09/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Grr\GrrBundle\Authorization\Helper;

use Grr\Core\Contrat\Entity\AreaInterface;
use Grr\Core\Contrat\Entity\RoomInterface;
use Grr\Core\Contrat\Entity\Security\AuthorizationInterface;
use Grr\Core\Contrat\Repository\AreaRepositoryInterface;
use Grr\Core\Contrat\Repository\RoomRepositoryInterface;
use Grr\Core\Contrat\Repository\Security\AuthorizationRepositoryInterface;
use Grr\Core\Security\SecurityRole;
use Grr\Core\Setting\SettingsRoom;
use Grr\GrrBundle\Area\Repository\AreaRepository;
use Grr\GrrBundle\Authorization\Repository\AuthorizationRepository;
use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Room\Repository\RoomRepository;
use Grr\Core\Contrat\Entity\Security\UserInterface;

class AuthorizationHelper
{
    /**
     * @var AuthorizationRepository
     */
    private $authorizationRepository;
    /**
     * @var RoomRepository
     */
    private $roomRepository;
    /**
     * @var AreaRepository
     */
    private $areaRepository;

    public function __construct(
        AuthorizationRepositoryInterface $authorizationRepository,
        AreaRepositoryInterface $areaRepository,
        RoomRepositoryInterface $roomRepository
    ) {
        $this->authorizationRepository = $authorizationRepository;
        $this->roomRepository = $roomRepository;
        $this->areaRepository = $areaRepository;
    }

    /**
     * @return AreaInterface[]
     */
    public function getAreasUserCanAdd(UserInterface $user): array
    {
        if ($user->hasRole(SecurityRole::ROLE_GRR_ADMINISTRATOR)) {
            return $this->areaRepository->findAll();
        }

        $areas = [];
        $authorizations = $this->authorizationRepository->findByUser($user);
        foreach ($authorizations as $authorization) {
            if (null !== $authorization->getArea()) {
                $areas[] = $authorization->getArea();
                continue;
            }
            if (null !== ($room = $authorization->getRoom())) {
                $area = $room->getArea();
                $areas[] = $area;
                continue;
            }
        }

        return $areas;
    }

    /**
     * @return RoomInterface[]
     */
    public function getRoomsUserCanAdd(UserInterface $user, ?AreaInterface $area = null): iterable
    {
        if ($user->hasRole(SecurityRole::ROLE_GRR_ADMINISTRATOR)) {
            if (null !== $area) {
                return $this->roomRepository->findByArea($area);
            }

            return $this->roomRepository->findAll();
        }

        $rooms = [[]];

        if (null !== $area) {
            $authorizations = $this->authorizationRepository->findByUserAndArea($user, $area);
        } else {
            $authorizations = $this->authorizationRepository->findByUser($user);
        }

        foreach ($authorizations as $authorization) {
            $area = $authorization->getArea();
            if (null !== $area) {
                $rooms[] = $area->getRooms()->toArray();
                continue;
            }
            if (null !== ($room = $authorization->getRoom())) {
                $rooms[] = [$room];
                continue;
            }
        }

        return array_merge(...$rooms);
    }

    /**
     * Tous les droits sur l'Area et ses ressources modifier ses paramètres, la supprimer
     * Peux encoder des entry dans toutes les ressources de l'Area.
     */
    public function isAreaAdministrator(UserInterface $user, AreaInterface $area): bool
    {
        return (bool)$this->authorizationRepository->findOneBy(
            ['user' => $user, 'area' => $area, 'isAreaAdministrator' => true]
        );
    }

    /**
     * Peux gérer les ressources mais pas modifier l'Area
     * Peux encoder des entry dans toutes les ressources de l'Area.
     */
    public function isAreaManager(UserInterface $user, AreaInterface $area): bool
    {
        if ($this->isAreaAdministrator($user, $area)) {
            return true;
        }

        return (bool)$this->authorizationRepository->findOneBy(
            ['user' => $user, 'area' => $area, 'isAreaAdministrator' => false]
        );
    }

    /**
     * Peux gérer la room (modifier les paramètres) et pas de contraintes pour encoder les entry.
     */
    public function isRoomAdministrator(UserInterface $user, RoomInterface $room): bool
    {
        if ($this->isAreaAdministrator($user, $room->getArea())) {
            return true;
        }

        return (bool)$this->authorizationRepository->findOneBy(
            ['user' => $user, 'room' => $room, 'isResourceAdministrator' => true]
        );
    }

    /**
     * Peux gérer toutes les entrées sans contraintes.
     */
    public function isRoomManager(UserInterface $user, RoomInterface $room): bool
    {
        if ($this->isRoomAdministrator($user, $room)) {
            return true;
        }

        if ($this->isAreaManager($user, $room->getArea())) {
            return true;
        }

        return (bool)$this->authorizationRepository->findOneBy(
            ['user' => $user, 'room' => $room, 'isResourceAdministrator' => false]
        );
    }

    public function isGrrAdministrator(UserInterface $user): bool
    {
        return $user->hasRole(SecurityRole::ROLE_GRR_ADMINISTRATOR);
    }

    public function checkAuthorizationRoomToAddEntry(RoomInterface $room, UserInterface $user = null): bool
    {
        $ruleToAdd = $room->getRuleToAdd();

        /*
         * Tout le monde peut encoder une réservation meme si pas connecte
         */
        if (SettingsRoom::CAN_ADD_EVERY_BODY === $ruleToAdd) {
            return true;
        }

        /*
         * A partir d'ici il faut être connecté
         */
        if (null === $user) {
            return false;
        }

        /*
         * Le user est il full identifie
         */
        if (SettingsRoom::CAN_ADD_EVERY_CONNECTED === $ruleToAdd) {
            return $user->hasRole(SecurityRole::ROLE_GRR);
        }

        /*
         * il faut être connecté et avoir le role @see SecurityRole::ROLE_GRR_ACTIVE_USER
         */
        if (SettingsRoom::CAN_ADD_EVERY_USER_ACTIVE === $ruleToAdd) {
            return $user->hasRole(SecurityRole::ROLE_GRR_ACTIVE_USER);
        }

        /*
         * Il faut être administrateur de la room
         */
        if (SettingsRoom::CAN_ADD_EVERY_ROOM_ADMINISTRATOR === $ruleToAdd) {
            return $this->isRoomAdministrator($user, $room);
        }

        /*
         * Il faut être manager de la room
         */
        if (SettingsRoom::CAN_ADD_EVERY_ROOM_MANAGER === $ruleToAdd) {
            return $this->isRoomManager($user, $room);
        }

        /*
         * Il faut être administrateur de l'area
         */
        if (SettingsRoom::CAN_ADD_EVERY_AREA_ADMINISTRATOR === $ruleToAdd) {
            $area = $room->getArea();

            return $this->isAreaAdministrator($user, $area);
        }

        /*
         * Il faut être manager de l'area
         */
        if (SettingsRoom::CAN_ADD_EVERY_AREA_MANAGER === $ruleToAdd) {
            $area = $room->getArea();

            return $this->isAreaManager($user, $area);
        }

        /*
         * Il faut être administrateur de Grr
         */
        if (SettingsRoom::CAN_ADD_EVERY_GRR_ADMINISTRATOR === $ruleToAdd) {
            return $this->isGrrAdministrator($user);
        }

        return false;
    }

    public function canAddEntry(RoomInterface $room, ?UserInterface $user = null): bool
    {
        $ruleToAdd = $room->getRuleToAdd();

        if ($user && $this->isGrrAdministrator($user)) {
            return true;
        }

        if (!$user || $ruleToAdd > SettingsRoom::CAN_ADD_NO_RULE) {
            return $this->checkAuthorizationRoomToAddEntry($room, $user);
        }

        if ($this->isGrrAdministrator($user)) {
            return true;
        }

        $area = $room->getArea();
        if ($this->isAreaAdministrator($user, $area)) {
            return true;
        }
        if ($this->isAreaManager($user, $area)) {
            return true;
        }
        if ($this->isRoomAdministrator($user, $room)) {
            return true;
        }

        return $this->isRoomManager($user, $room);
    }

    public function canSeeRoom(): bool
    {
        return true;
    }

    public function isAreaRestricted(Area $area): bool
    {
        return $area->getIsRestricted();
    }

    /**
     * @todo
     */
    public function canSeeArea(): bool
    {
        return true;
    }

    /**
     * @todo
     */
    public function canSeeAreaRestricted(): bool
    {
        return true;
    }

    /**
     * @param AreaInterface $area
     * @param RoomInterface $room
     * @return AuthorizationInterface[]
     */
    public function findByAreaOrRoom(AreaInterface $area, RoomInterface $room): array
    {
        return $this->authorizationRepository->findByAreaOrRoom($area, $room);
    }

}
