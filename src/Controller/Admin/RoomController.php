<?php

namespace Grr\GrrBundle\Controller\Admin;

use Grr\Core\Contrat\Repository\RoomRepositoryInterface;
use Grr\Core\Room\Message\RoomCreated;
use Grr\Core\Room\Message\RoomDeleted;
use Grr\Core\Room\Message\RoomUpdated;
use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\Room;
use Grr\GrrBundle\Room\Factory\RoomFactory;
use Grr\GrrBundle\Room\Form\RoomType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/room')]
class RoomController extends AbstractController
{
    public function __construct(
        private RoomFactory $roomFactory,
        private RoomRepositoryInterface $roomRepository,
        private MessageBusInterface $messageBus
    ) {
    }

    #[Route(path: '/new/{id}', name: 'grr_admin_room_new', methods: ['GET', 'POST'])]
    #[IsGranted(data: 'grr.area.new.room', subject: 'area')]
    public function new(Request $request, Area $area): Response
    {
        $room = $this->roomFactory->createNew($area);
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->roomRepository->persist($room);
            $this->roomRepository->flush();
            $this->messageBus->dispatch(new RoomCreated($room->getId()));

            return $this->redirectToRoute('grr_admin_room_show', [
                'id' => $room->getId(),
            ]);
        }

        return $this->render(
            '@grr_admin/room/new.html.twig',
            [
                'area' => $area,
                'room' => $room,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'grr_admin_room_show', methods: ['GET'])]
    #[IsGranted(data: 'grr.room.show', subject: 'room')]
    public function show(Room $room): Response
    {
        return $this->render(
            '@grr_admin/room/show.html.twig',
            [
                'room' => $room,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'grr_admin_room_edit', methods: ['GET', 'POST'])]
    #[IsGranted(data: 'grr.room.edit', subject: 'room')]
    public function edit(Request $request, Room $room): Response
    {
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->roomRepository->flush();

            $this->messageBus->dispatch(new RoomUpdated($room->getId()));

            return $this->redirectToRoute(
                'grr_admin_room_show',
                [
                    'id' => $room->getId(),
                ]
            );
        }

        return $this->render(
            '@grr_admin/room/edit.html.twig',
            [
                'room' => $room,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'grr_admin_room_delete', methods: ['POST'])]
    #[IsGranted(data: 'grr.room.delete', subject: 'room')]
    public function delete(Request $request, Room $room): RedirectResponse
    {
        $area = $room->getArea();
        if ($this->isCsrfTokenValid('delete'.$room->getId(), $request->request->get('_token'))) {
            $id = $room->getId();
            foreach ($room->getEntries() as $entry) {
                $this->roomRepository->remove($entry);
            }
            $this->roomRepository->remove($room);
            $this->roomRepository->flush();

            $this->messageBus->dispatch(new RoomDeleted($id));
        }

        return $this->redirectToRoute('grr_admin_area_show', [
            'id' => $area->getId(),
        ]);
    }
}
