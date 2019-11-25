<?php

namespace Grr\GrrBundle\Security;

use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\Room;
use Grr\GrrBundle\Entity\Security\User;
use Grr\GrrBundle\Repository\Security\AuthorizationRepository;
use Grr\Core\Setting\SettingsRoom;
use Symfony\Component\Security\Core\User\UserInterface;

class SecurityHelper
{
    /**
     * @var AuthorizationRepository
     */
    private $authorizationRepository;

    public function __construct(AuthorizationRepository $authorizationRepository)
    {
        $this->authorizationRepository = $authorizationRepository;
    }

    /**
     * Tous les droits sur l'Area et ses ressources modifier ses paramètres, la supprimer
     * Peux encoder des entry dans toutes les ressources de l'Area.
     */
    public function isAreaAdministrator(UserInterface $user, Area $area): bool
    {
        return (bool) $this->authorizationRepository->findOneBy(
            ['user' => $user, 'area' => $area, 'isAreaAdministrator' => true]
        );
    }

    /**
     * Peux gérer les ressources mais pas modifier l'Area
     * Peux encoder des entry dans toutes les ressources de l'Area.
     */
    public function isAreaManager(UserInterface $user, Area $area): bool
    {
        if ($this->isAreaAdministrator($user, $area)) {
            return true;
        }
        return (bool) $this->authorizationRepository->findOneBy(
            ['user' => $user, 'area' => $area, 'isAreaAdministrator' => false]
        );
    }

    /**
     * Peux gérer la room (modifier les paramètres) et pas de contraintes pour encoder les entry.
     */
    public function isRoomAdministrator(UserInterface $user, Room $room): bool
    {
        if ($this->isAreaAdministrator($user, $room->getArea())) {
            return true;
        }
        return (bool) $this->authorizationRepository->findOneBy(
            ['user' => $user, 'room' => $room, 'isResourceAdministrator' => true]
        );
    }

    /**
     * Peux gérer toutes les entrées sans contraintes.
     */
    public function isRoomManager(UserInterface $user, Room $room): bool
    {
        if ($this->isRoomAdministrator($user, $room)) {
            return true;
        }

        if ($this->isAreaManager($user, $room->getArea())) {
            return true;
        }
        return (bool) $this->authorizationRepository->findOneBy(
            ['user' => $user, 'room' => $room, 'isResourceAdministrator' => false]
        );
    }

    public function isGrrAdministrator(UserInterface $user): bool
    {
        return $user->hasRole(SecurityRole::ROLE_GRR_ADMINISTRATOR);
    }

    /**
     * @param User|null $user
     */
    public function checkAuthorizationRoomToAddEntry(Room $room, UserInterface $user = null): bool
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
        if ($user === null) {
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

    /**
     * @param User|null $user
     */
    public function canAddEntry(Room $room, ?UserInterface $user = null): bool
    {
        $rule = $room->getRuleToAdd();

        if ($user && $this->isGrrAdministrator($user)) {
            return true;
        }

        if (!$user || $rule > SettingsRoom::CAN_ADD_NO_RULE) {
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

    public function canSeeRoom(Room $room, UserInterface $user = null): bool
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
    public function canSeeArea(Area $area, UserInterface $user): bool
    {
        return true;
    }

    /**
     * @todo
     */
    public function canSeeAreaRestricted(Area $area, UserInterface $user): bool
    {
        return true;
    }
}
