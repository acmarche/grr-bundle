<?php

namespace Grr\GrrBundle\Controller\Admin;

use Grr\Core\Area\Message\AreaCreated;
use Grr\Core\Area\Message\AreaDeleted;
use Grr\Core\Area\Message\AreaUpdated;
use Grr\Core\Contrat\Repository\AreaRepositoryInterface;
use Grr\Core\Contrat\Repository\RoomRepositoryInterface;
use Grr\GrrBundle\Area\Factory\AreaFactory;
use Grr\GrrBundle\Area\Form\AreaType;
use Grr\GrrBundle\Authorization\Helper\AuthorizationHelper;
use Grr\GrrBundle\Entity\Area;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/admin/area')]
class AreaController extends AbstractController
{
    public function __construct(
        private readonly AreaFactory $areaFactory,
        private readonly AreaRepositoryInterface $areaRepository,
        private readonly RoomRepositoryInterface $roomRepository,
        private readonly AuthorizationHelper $authorizationHelper,
        private readonly MessageBusInterface $messageBus
    ) {
    }

    #[Route(path: '/', name: 'grr_admin_area_index', methods: ['GET'])]
    #[IsGranted('grr.area.index')]
    public function index(): Response
    {
        $user = $this->getUser();
        $areas = $this->authorizationHelper->getAreasUserCanAdd($user);

        return $this->render(
            '@grr_admin/area/index.html.twig',
            [
                'areas' => $areas,
            ]
        );
    }

    #[Route(path: '/new', name: 'grr_admin_area_new', methods: ['GET', 'POST'])]
    #[IsGranted('grr.area.new')]
    public function new(Request $request): Response
    {
        $area = $this->areaFactory->createNew();
        $form = $this->createForm(AreaType::class, $area);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->areaRepository->persist($area);
            $this->areaRepository->flush();

            $this->messageBus->dispatch(new AreaCreated($area->getId()));

            return $this->redirectToRoute('grr_admin_area_show', [
                'id' => $area->getId(),
            ]);
        }

        return $this->render(
            '@grr_admin/area/new.html.twig',
            [
                'area' => $area,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}', name: 'grr_admin_area_show', methods: ['GET'])]
    #[IsGranted('grr.area.show', subject: 'area')]
    public function show(Area $area): Response
    {
        $rooms = $this->roomRepository->findBy([
            'area' => $area,
        ]);

        return $this->render(
            '@grr_admin/area/show.html.twig',
            [
                'area' => $area,
                'rooms' => $rooms,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'grr_admin_area_edit', methods: ['GET', 'POST'])]
    #[IsGranted('grr.area.edit', subject: 'area')]
    public function edit(Request $request, Area $area): Response
    {
        $form = $this->createForm(AreaType::class, $area);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->areaRepository->flush();

            $this->messageBus->dispatch(new AreaUpdated($area->getId()));

            return $this->redirectToRoute(
                'grr_admin_area_show',
                [
                    'id' => $area->getId(),
                ]
            );
        }

        return $this->render(
            '@grr_admin/area/edit.html.twig',
            [
                'area' => $area,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}', name: 'grr_admin_area_delete', methods: ['POST'])]
    #[IsGranted('grr.area.delete', subject: 'area')]
    public function delete(Request $request, Area $area): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$area->getId(), $request->request->get('_token'))) {
            $id = $area->getId();
            foreach ($area->getRooms() as $room) {
                $this->areaRepository->remove($room);
            }

            $this->areaRepository->remove($area);
            $this->areaRepository->flush();
            $this->messageBus->dispatch(new AreaDeleted($id));
        }

        return $this->redirectToRoute('grr_admin_area_index');
    }
}
