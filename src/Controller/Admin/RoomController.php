<?php

namespace Grr\GrrBundle\Controller\Admin;

use Grr\Core\Room\Message\RoomCreated;
use Grr\Core\Room\Message\RoomDeleted;
use Grr\Core\Room\Message\RoomUpdated;
use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\Room;
use Grr\GrrBundle\Room\Factory\RoomFactory;
use Grr\GrrBundle\Room\Form\RoomType;
use Grr\GrrBundle\Room\Manager\RoomManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/room")
 */
class RoomController extends AbstractController
{
    /**
     * @var RoomFactory
     */
    private $roomFactory;
    /**
     * @var RoomManager
     */
    private $roomManager;

    public function __construct(
        RoomFactory $roomFactory,
        RoomManager $roomManager
    ) {
        $this->roomFactory = $roomFactory;
        $this->roomManager = $roomManager;
    }

    /**
     * @Route("/new/{id}", name="grr_admin_room_new", methods={"GET", "POST"})
     * @IsGranted("grr.area.new.room", subject="area")
     */
    public function new(Request $request, Area $area): Response
    {
        $room = $this->roomFactory->createNew($area);

        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->roomManager->insert($room);

            $this->dispatchMessage(new RoomCreated($room->getId()));

            return $this->redirectToRoute('grr_admin_room_show', ['id' => $room->getId()]);
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

    /**
     * @Route("/{id}", name="grr_admin_room_show", methods={"GET"})
     * @IsGranted("grr.room.show", subject="room")
     */
    public function show(Room $room): Response
    {
        return $this->render(
            '@grr_admin/room/show.html.twig',
            [
                'room' => $room,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="grr_admin_room_edit", methods={"GET", "POST"})
     * @IsGranted("grr.room.edit", subject="room")
     */
    public function edit(Request $request, Room $room): Response
    {
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->roomManager->flush();

            $this->dispatchMessage(new RoomUpdated($room->getId()));

            return $this->redirectToRoute(
                'grr_admin_room_show',
                ['id' => $room->getId()]
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

    /**
     * @Route("/{id}", name="grr_admin_room_delete", methods={"DELETE"})
     * @IsGranted("grr.room.delete", subject="room")
     */
    public function delete(Request $request, Room $room): Response
    {
        $area = $room->getArea();
        if ($this->isCsrfTokenValid('delete'.$room->getId(), $request->request->get('_token'))) {
            $id = $room->getId();
            $this->roomManager->removeEntries($room);
            $this->roomManager->remove($room);
            $this->roomManager->flush();

            $this->dispatchMessage(new RoomDeleted($id));
        }

        return $this->redirectToRoute('grr_admin_area_show', ['id' => $area->getId()]);
    }
}
