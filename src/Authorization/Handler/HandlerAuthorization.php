<?php

namespace Grr\GrrBundle\Authorization\Handler;

use Doctrine\Common\Collections\ArrayCollection;
use Grr\Core\Contrat\Repository\Security\AuthorizationRepositoryInterface;
use Grr\Core\Model\AuthorizationModel;
use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\Room;
use Grr\GrrBundle\Entity\Security\Authorization;
use Grr\GrrBundle\Entity\Security\User;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class HandlerAuthorization
{
    private AuthorizationRepositoryInterface $authorizationRepository;
    private FlashBagInterface $flashBag;
    private TranslatorInterface $translator;
    private ?bool $error = null;

    public function __construct(
        AuthorizationRepositoryInterface $authorizationRepository,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator
    ) {
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
        $arrayCollection = $data->getRooms();

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

            if (count($arrayCollection) > 0) {
                $this->executeForRooms($authorization, $area, $arrayCollection, $user);
            } else {
                $this->executeForArea($authorization, $area, $user);
            }
        }

        if (!$this->error) {
            $this->flashBag->add('success', 'flash.authorization.created');
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
                $this->authorizationRepository->persist($copy);
                $this->authorizationRepository->flush();
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
            $this->authorizationRepository->persist($authorization);
            $this->authorizationRepository->flush();
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
