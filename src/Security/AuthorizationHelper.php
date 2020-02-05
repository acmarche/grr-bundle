<?php
/**
 * This file is part of GrrSf application.
 *
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 26/09/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Grr\GrrBundle\Security;

use Grr\Core\Security\SecurityRole;
use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Repository\AreaRepository;
use Grr\GrrBundle\Repository\RoomRepository;
use Grr\GrrBundle\Repository\Security\AuthorizationRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthorizationHelper
{
    /**
     * @var Security
     */
    private $security;
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
        Security $security,
        AuthorizationRepository $authorizationRepository,
        AreaRepository $areaRepository,
        RoomRepository $roomRepository
    ) {
        $this->security = $security;
        $this->authorizationRepository = $authorizationRepository;
        $this->roomRepository = $roomRepository;
        $this->areaRepository = $areaRepository;
    }

    /**
     * @throws \Exception
     *
     * @return \Grr\Core\Contrat\AreaInterface[]|\Grr\Core\Contrat\AreaInterface[]|null[]
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
     * @throws \Exception
     *
     * @return \Grr\Core\Contrat\RoomInterface[]|mixed[]
     */
    public function getRoomsUserCanAdd(UserInterface $user, ?Area $area = null): iterable
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
}
