<?php

namespace Grr\GrrBundle\Security;

use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\Room;
use Grr\GrrBundle\Entity\Security\Authorization;
use Grr\GrrBundle\Entity\Security\User;
use Grr\GrrBundle\Manager\AuthorizationManager;
use Grr\Core\Model\AuthorizationModel;
use Grr\GrrBundle\Repository\Security\AuthorizationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class HandlerAuthorization
{
    /**
     * @var AuthorizationManager
     */
    private $authorizationManager;
    /**
     * @var AuthorizationRepository
     */
    private $authorizationRepository;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var bool
     */
    private $error;

    public function __construct(
        AuthorizationRepository $authorizationRepository,
        AuthorizationManager $authorizationManager,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator
    ) {
        $this->authorizationManager = $authorizationManager;
        $this->authorizationRepository = $authorizationRepository;
        $this->flashBag = $flashBag;
        $this->translator = $translator;
    }

    public function handle(FormInterface $form): void
    {
        /**
         * @var AuthorizationModel
         */
        $data = $form->getData();

        /**
         * @var User[]|ArrayCollection
         */
        $users = $data->getUsers();

        /**
         * @var Area
         */
        $area = $data->getArea();

        /**
         * @var Room[]|array
         */
        $rooms = $data->getRooms();

        /**
         * @var int
         */
        $role = $data->getRole();

        $this->error = false;

        foreach ($users as $user) {
            $authorization = new Authorization();
            $authorization->setUser($user);

            if (1 === $role) {
                $authorization->setIsAreaAdministrator(true);
            }
            if (2 === $role) {
                $authorization->setIsResourceAdministrator(true);
            }

            if (count($rooms) > 0) {
                $this->executeForRooms($authorization, $area, $rooms, $user);
            } else {
                $this->executeForArea($authorization, $area, $user);
            }
        }

        if (!$this->error) {
            $this->flashBag->add('success', 'authorization.flash.new');
        }
    }

    protected function executeForRooms(
        Authorization $authorization,
        Area $area,
        iterable $rooms,
        $user
    ): void {
        if ($this->existArea($user, $area)) {
            $this->error = true;
            $this->flashBag->add(
                'danger',
                $this->translator->trans(
                    'authorization.area.exist',
                    [
                        'user' => $user,
                        'area' => $area,
                    ]
                )
            );

            return;
        }
        foreach ($rooms as $room) {
            $copy = clone $authorization;
            if ($this->existRoom($user, $room)) {
                $this->error = true;
                $this->flashBag->add(
                    'danger',
                    $this->translator->trans('authorization.room.exist', ['user' => $user, 'room' => $room])
                );
            } else {
                $copy->setRoom($room);
                $this->authorizationManager->insert($copy);
            }
        }
    }

    protected function executeForArea(Authorization $authorization, Area $area, UserInterface $user): void
    {
        if ($this->existArea($user, $area)) {
            $this->error = true;
            $this->flashBag->add(
                'danger',
                $this->translator->trans(
                    'authorization.area.exist',
                    [
                        'user' => $user,
                        'area' => $area,
                    ]
                )
            );
        } else {
            $authorization->setArea($area);
            $this->authorizationManager->insert($authorization);
        }
    }

    protected function existArea(UserInterface $user, Area $area): bool
    {
        $count = count($this->authorizationRepository->findBy(['user' => $user, 'area' => $area]));

        return $count > 0;
    }

    protected function existRoom(UserInterface $user, Room $room): bool
    {
        $count = count($this->authorizationRepository->findBy(['user' => $user, 'room' => $room]));

        return $count > 0;
    }
}
